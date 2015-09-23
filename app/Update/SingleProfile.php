<?php

namespace VacStatus\Update;

use VacStatus\Models\Profile;
use VacStatus\Models\UserListProfile;
use VacStatus\Models\User;
use VacStatus\Models\ProfileBan;
use VacStatus\Models\ProfileOldAlias;

use Cache;

use VacStatus\Steam\Steam;
use VacStatus\Steam\SteamAPI;

use DateTime;
use DateInterval;

class SingleProfile extends BaseUpdate
{
	protected $smallId;

	function __construct($smallId)
	{
		$this->smallId = (int) $smallId;
		$this->cacheName = "profile_$smallId";
	}

	public function getProfile()
	{
		return $this->updateUsingAPI();

		$return = $this->grabCache();

		if($return !== false) return $return;
		return $this->grabFromDB();
	}

	protected function grabCache()
	{
		if(!Cache::has($this->cacheName)) return false;

		$profileCache = Cache::get($this->cacheName);

		$profileCache = array_merge($profileCache, $this->getTimesAdded($profileCache['id']));

		return $profileCache;
	}

	private function getTimesAdded($profileId)
	{
		$gettingCount = UserListProfile::whereProfileId($profileId)
			->whereNull('deleted_at')
			->orderBy('id','desc')
			->get();

		return [
			'total' => $gettingCount->count(),
			'time' => isset($gettingCount[0]) ? (new DateTime($gettingCount[0]->created_at))->format("M j Y") : null
		];
	}

	private function cleanOldAlias($profileOldAlias)
	{
		$oldAliasArray = [];

		foreach($profileOldAlias as $k => $oldAlias)
		{
			$oldAliasArray[] = [
				"newname" => $oldAlias->seen_alias,
				"timechanged" => $oldAlias->seen->format("M j Y")
			];
		}

		return $oldAliasArray;
	}

	private function updateUsingAPI()
	{
		/**
		 * Prepare the STEAM WEB API call
		 */
		$steamAPI = new SteamAPI($this->smallId, true);

		/**
		 * Grab 'info' from STEAM WEB API
		 * Stops if there is an error
		 */
		$steamInfo = $steamAPI->fetch('info');

		if(isset($steamInfo['error'])) return $this->error($steamInfo['error']);
		if(!isset($steamInfo['response']['players'][0])) return $this->error('profile_null');

		$steamInfo = $steamInfo['response']['players'][0];

		/**
		 * Grab 'ban' from STEAM WEB API
		 * Stops if there is an error
		 */
		$steamBan = $steamAPI->fetch('ban');

		if(isset($steamBan['error'])) return $this->error($steamBan['error']);
		if(!isset($steamBan['players'][0])) return $this->error('profile_null');

		$steamBan = $steamBan['players'][0];

		/**
		 * Grab 'alias' from STEAM WEB API
		 * Does not stop when error (this call is unstable)
		 */
		$steamAlias = $steamAPI->fetch('alias');

		if(!isset($steamAlias['error'])) usort($steamAlias, array('VacStatus\Steam\Steam', 'aliasSort'));
		else $steamAlias = [];

		/**
		 * STEAM WEB API calls stop here
		 * Start updating / creating data
		 */

		/**
		 * Fetch first matching profile
		 * or create a new class (but don't save yet)
		 */
		$profile = Profile::firstOrNew(['small_id' => $this->smallId]);

		if(isset($steamInfo['timecreated'])) $profile->profile_created = $steamInfo['timecreated'];

		$profile->display_name = $steamInfo['personaname'];
		$profile->avatar = Steam::imgToHTTPS($steamInfo['avatarfull']);
		$profile->avatar_thumb = Steam::imgToHTTPS($steamInfo['avatar']);
		$profile->privacy = $steamInfo['communityvisibilitystate'];
		$profile->alias = json_encode($steamAlias);

		if(!$profile->save()) return $this->error('profile_save_error');

		/**
		 * Now start inserting profile's ban data
		 */
		$profileBan = $profile->ProfileBan;

		/**
		 * Dont update the profile_ban if there is nothing to update
		 * This has to do with in the future when I check for new bans to notify/email
		 */
		$skipProfileBan = true;

		$apiLatestBanDate = new DateTime();
		$apiLatestBanDate->sub(new DateInterval("P{$steamBan['DaysSinceLastBan']}D"));


		$apiVacBans = (int) $steamBan['NumberOfVACBans'];
		$apiGameBans = (int) $steamBan['NumberOfGameBans'];

		if(!isset($profileBan->id))
		{
			$profileBan = new ProfileBan;
			$profileBan->profile_id = $profile->id;
			$skipProfileBan = false;
		} else {

			if($profileBan->community != $steamBan['CommunityBanned']
			   || $profileBan->trade != ($steamBan['EconomyBan'] != 'none'))
			{
				$skipProfileBan = false;
			}

			if(($profileBan->vac_bans != 0 || $profileBan->game_bans != 0)
			   && $profileBan->last_ban_date->format('Y-m-d') !== $apiLatestBanDate->format('Y-m-d'))
			{
				$skipProfileBan = false;
			}

			if($profileBan->vac_bans != $apiVacBans
			   || $profileBan->game_bans != $apiGameBans)
			{
				$skipProfileBan = false;
				$profileBan->timestamps = true;
			}

			if($profileBan->vac_bans >= $apiVacBans
			   && $profileBan->game_bans >= $apiGameBans)
			{
				$profileBan->timestamps = false;
			}
		}

		$profileBan->vac_bans = $apiVacBans;
		$profileBan->game_bans = $apiGameBans;
		$profileBan->last_ban_date = $apiLatestBanDate->format('Y-m-d');
		$profileBan->community = $steamBan['CommunityBanned'];
		$profileBan->trade = $steamBan['EconomyBan'] != 'none';

		if(!$skipProfileBan) if(!$profile->ProfileBan()->save($profileBan)) return $this->error('ban_save_error');

		/**
		 * Add current alias to the DB
		 */
		$profileOldAlias = $profile->ProfileOldAlias;

		$currentTime = new DateTime();

		if($profileOldAlias->count() == 0)
		{
			$newAlias = new ProfileOldAlias;
			$newAlias->profile_id = $profile->id;
			$newAlias->seen = $currentTime->format('Y-m-d');
			$newAlias->seen_alias = $profile->display_name;
		} else {
			$matchFound = false;

			foreach($profileOldAlias as $oldAlias)
			{
				if(!is_object($oldAlias)) continue;

				// Compare the current display name with the alias
				// that current exists on the DB
				if($oldAlias->seen_alias == $profile->display_name)
				{
					$matchFound = true;
					break;
				}
			}

			if(!$matchFound)
			{
				$newAlias = new ProfileOldAlias;
				$newAlias->profile_id = $profile->id;
				$newAlias->seen = $currentTime->format('Y-m-d');
				$newAlias->seen_alias = $profile->display_name;
			}
		}

		$oldAliasArray = $this->cleanOldAlias($profileOldAlias);

		if(isset($newAlias)) 
		{
			$profile->ProfileOldAlias()->save($newAlias);

			$oldAliasArray[] = [
				"newname" => $newAlias->seen_alias,
				"timechanged" => $newAlias->seen->format("M j Y")
			];
		}

		/**
		 * Fetch profile's VacStatus account
		 */
		$user = User::where('small_id', $this->smallId)->first();

		/**
		 * Prepare to send data to client
		 * Also save it on cache for set # of minutes
		 */

		$return = [
			'id'				=> $profile->id,
			'display_name'		=> $profile->display_name,
			'avatar'			=> $profile->avatar,
			'avatar_thumb'		=> $profile->avatar_thumb,
			'small_id'			=> $profile->small_id,
			'steam_64_bit'		=> $profile->steam_64_bit,
			'steam_32_bit'		=> $profile->steam_32_bit,
			'profile_created'	=> $profile->profile_created,
			'privacy'			=> $profile->privacy,
			'alias'				=> $profile->alias,
			'created_at'		=> $profile->created_at,

			'vac_bans'			=> $profileBan->vac_bans,
			'game_bans'			=> $profileBan->game_bans,
			'last_ban_date'		=> $profileBan->last_ban_date->format("M j Y"),
			'community'			=> $profileBan->community,
			'trade'				=> $profileBan->trade,

			'site_admin'		=> $user->site_admin,
			'donation'			=> $user->donation,
			'beta'				=> $user->beta,
			'profile_old_alias'	=> array_reverse($oldAliasArray),
		];

		$return = array_merge($return, $this->getTimesAdded($profile->id));

		/* YAY nothing broke :D time to return the data (and update cache) */
		$this->updateCache($return);
		return $return;
	}

	private function grabFromDB()
	{
		$profile = Profile::where('profile.small_id', $this->smallId)->getProfileData();

		$oldAliasArray = $this->cleanOldAlias($profile->ProfileOldAlias);

		$return = [
			'id'				=> $profile->id,
			'display_name'		=> $profile->display_name,
			'avatar'			=> $profile->avatar,
			'small_id'			=> $profile->small_id,
			'steam_64_bit'		=> $profile->steam_64_bit,
			'steam_32_bit'		=> $profile->steam_32_bit,
			'profile_created'	=> $profile->profile_created,
			'privacy'			=> $profile->privacy,
			'alias'				=> $profile->alias,
			'created_at'		=> $profile->created_at,

			'vac_bans'			=> $profile->vac_bans,
			'game_bans'			=> $profile->game_bans,
			'last_ban_date'		=> $profile->last_ban_date->format("M j Y"),
			'community'			=> $profile->community,
			'trade'				=> $profile->trade,
			
			'site_admin'		=> $profile->site_admin,
			'donation'			=> $profile->donation,
			'beta'				=> $profile->beta,
			'profile_old_alias'	=> array_reverse($oldAliasArray),
		];

		$return = array_merge($return, $this->getTimesAdded($profile->id));

		return $return;
	}
}