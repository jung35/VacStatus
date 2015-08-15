<?php namespace VacStatus\Update;

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
		
		// if($this->canUpdate()) return $this->updateUsingAPI();

		$return = $this->grabCache();

		if($return !== false) return $return;
		return $this->grabFromDB();
	}

	protected function grabCache()
	{
		if(!Cache::has($this->cacheName)) return false;

		$profileCache = Cache::get($this->cacheName);

		$profileCache['times_added'] = $this->getTimesAdded($profileCache['id']);

		return $profileCache;
	}

	private function getTimesAdded($profileId)
	{
		$gettingCount = UserListProfile::whereProfileId($profileId)
			->orderBy('id','desc')
			->get();

		return [
			'number' => $gettingCount->count(),
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
				$profileBan->timestamps = false;
			}

			if($profileBan->last_ban_date->format('Y-m-d') !== $apiLatestBanDate->format('Y-m-d'))
			{
				$profileBan->timestamps = false;
				$skipProfileBan = false;
			}

			if($profileBan->vac_bans != $apiVacBans
			   || $profileBan->game_bans != $apiGameBans)
			{
				$profileBan->timestamps = true;
				$skipProfileBan = false;
			}

			if($profileBan->vac_bans > $apiVacBans
			   || $profileBan->game_bans > $apiGameBans)
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

		$steam64BitId = Steam::to64Bit($profile->small_id);

		$return = [
			'id'				=> $profile->id,
			'display_name'		=> $profile->display_name,
			'avatar'			=> $profile->avatar,
			'avatar_thumb'		=> $profile->avatar_thumb,
			'small_id'			=> $this->smallId,
			'steam_64_bit'		=> $steam64BitId,
			'steam_32_bit'		=> Steam::to32Bit($steam64BitId),
			'profile_created'	=> isset($profile->profile_created) ? date("M j Y", $profile->profile_created) : "Unknown",
			'privacy'			=> $profile->privacy,
			'alias'				=> Steam::friendlyAlias($steamAlias),
			'created_at'		=> $profile->created_at->format("M j Y"),

			'vac_bans'			=> $profileBan->vac_bans,
			'game_bans'			=> $profileBan->game_bans,
			'last_ban_date'		=> $profileBan->last_ban_date->format("M j Y"),
			'community'			=> $profileBan->community,
			'trade'				=> $profileBan->trade,

			'site_admin'		=> isset($user->id) ? $user->site_admin : 0,
			'donation'			=> isset($user->id) ? $user->donation : 0,
			'beta'				=> isset($user->id) ? $user->beta : 0,
			'profile_old_alias'	=> array_reverse($oldAliasArray),

			'times_added'		=> $this->getTimesAdded($profile->id),
		];

		/* YAY nothing broke :D time to return the data (and update cache) */
		$this->updateCache($return);
		return $return;
	}

	private function grabFromDB()
	{
		$profile = Profile::where('profile.small_id', $this->smallId)
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->first([
				'profile.id',
				'profile.display_name',
				'profile.avatar',
				'profile.avatar_thumb',
				'profile.small_id',
				'profile.profile_created',
				'profile.privacy',
				'profile.alias',
				'profile.created_at',

				'profile_ban.vac_bans',
				'profile_ban.game_bans',
				'profile_ban.last_ban_date',
				'profile_ban.community',
				'profile_ban.trade',

				'users.site_admin',
				'users.donation',
				'users.beta',
			]);

		$steam64BitId = Steam::to64Bit($profile->small_id);
		$oldAliasArray = $this->cleanOldAlias($profile->ProfileOldAlias);

		$return = [
			'id'				=> $profile->id,
			'display_name'		=> $profile->display_name,
			'avatar'			=> $profile->avatar,
			'small_id'			=> $profile->small_id,
			'steam_64_bit'		=> $steam64BitId,
			'steam_32_bit'		=> Steam::to32Bit($steam64BitId),
			'profile_created'	=> isset($profile->profile_created) ? date("M j Y", $profile->profile_created) : "Unknown",
			'privacy'			=> $profile->privacy,
			'alias'				=> Steam::friendlyAlias(json_decode($profile->alias, true)),
			'created_at'		=> $profile->created_at->format("M j Y"),

			'vac_bans'			=> $profile->vac_bans,
			'game_bans'			=> $profile->game_bans,
			'last_ban_date'		=> $profile->last_ban_date->format("M j Y"),
			'community'			=> $profile->community,
			'trade'				=> $profile->trade,
			
			'site_admin'		=> $profile->site_admin,
			'donation'			=> $profile->donation,
			'beta'				=> $profile->beta,

			'profile_old_alias'	=> array_reverse($oldAliasArray),
			'times_added'		=> $this->getTimesAdded($profile->id),
		];

		return $return;
	}
}