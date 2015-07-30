<?php namespace VacStatus\Update;

use VacStatus\Models\Profile;
use VacStatus\Models\ProfileOldAlias;
use VacStatus\Models\UserListProfile;
use VacStatus\Models\ProfileBan;

use VacStatus\Steam\Steam;
use VacStatus\Steam\SteamAPI;

use Cache;
use Carbon;
use DateTime;
use DateInterval;

class MultiProfile extends BaseUpdate
{
	protected $profiles;
	protected $cacheName = "profile_";
	protected $refreshProfiles = [];
	private $customList;

	function __construct($profiles, $customList = false)
	{
		$this->profiles = $profiles;
		$this->customList = $customList;
	}

	public function run()
	{
		$this->getUpdateAbleProfiles();
		$updatedProfiles = $this->updateUsingAPI();

		if(isset($updatedProfiles['error']))
		{
			if($updatedProfiles['error'] == 'profile_null') $updatedProfiles = [];
			else return $this->error($updatedProfiles['error']);
		}

		return array_replace($this->profiles, $updatedProfiles);
	}

	protected function canUpdate($smallId = 0)
	{
		if(Cache::has($this->cacheName . $smallId)) return false;

		return true;
	}

	protected function updateCache($smallId = 0, $data = [])
	{
		$cacheName = $this->cacheName . $smallId;
		if(Cache::has($cacheName)) Cache::forget($cacheName);

		$expireTime = Carbon::now()->addMinutes($this->cacheLength);

		Cache::put($cacheName, $data, $expireTime);
	}

	private function getUpdateAbleProfiles()
	{
		$refreshProfiles = [];

		foreach($this->profiles as $k => $profile)
		{
			if(!$this->canUpdate($profile['small_id']) && count($profile) != 1) continue;

			$refreshProfiles[] = [
				'profile_key' => $k,
				'profile' => $profile 
			];
		}

		$this->refreshProfiles = $refreshProfiles;
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
		 * Sort out the small ID and save the key each profile belongs to
		 * The API doesn't like to give the data in the order requestd.
		 */
		$getSmallId = [];
		foreach($this->refreshProfiles as $profile)
		{
			$smallId = $profile['profile']['small_id'];
			$key = $profile['profile_key'];

			$getSmallId[] = (int) $smallId;
			$toSaveKey[$smallId] = $key;
		}

		/**
		 * Prepare the STEAM WEB API call
		 */
		$steamAPI = new SteamAPI($getSmallId, true);

		/**
		 * Grab 'info' from STEAM WEB API
		 * Stops if there is an error
		 */
		$steamInfos = $steamAPI->fetch('info');

		if(isset($steamInfos['error'])) return $this->error($steamInfos['error']);
		if(!isset($steamInfos['response']['players'][0])) return $this->error('profile_null');

		$steamInfos = $steamInfos['response']['players'];

		/**
		 * Grab 'ban' from STEAM WEB API
		 * Stops if there is an error
		 */
		$steamBans = $steamAPI->fetch('ban');

		if(isset($steamBans['error'])) return $this->error($steamBans['error']);
		if(!isset($steamBans['players'][0])) return $this->error('profile_null');

		$steamBans = $steamBans['players'];

		$profiles = Profile::whereIn('profile.small_id', $getSmallId)
			->groupBy('profile.id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->leftjoin('user_list_profile', 'user_list_profile.profile_id', '=', 'profile.id')
			->whereNull('user_list_profile.deleted_at')
			->get([
				'profile.id',
				'profile.small_id',
				'profile.display_name',
				'profile.privacy',
				'profile.avatar_thumb',
				'profile.avatar',
				'profile.profile_created',
				'profile.alias',
				'profile.created_at',

				'users.site_admin',
				'users.donation',
				'users.beta',

				\DB::raw('max(user_list_profile.created_at) as last_added_created_at'),
				\DB::raw('count(user_list_profile.id) as total')
			]);

		$profileBans = ProfileBan::whereIn('profile_id', $profiles->lists('id'))->get();
		$profileOldAliases = ProfileOldAlias::whereIn('profile_id', $profiles->lists('id'))->get();

		if($this->customList)
		{
			$userDescriptions = UserListProfile::whereUserListId($this->customList)->get();
		}

		$indexSave = [];

		foreach($steamInfos as $k => $info)
		{
			$indexSave[Steam::toSmallId($info['steamid'])] = ['steamInfos' => $k];
		}

		foreach($steamBans as $k => $ban)
		{
			// Lets just not update if api didn't return for this user
			if(!isset($indexSave[Steam::toSmallId($ban['SteamId'])])) continue;
			$indexSave[Steam::toSmallId($ban['SteamId'])]['steamBans'] = $k;
		}

		$newProfiles = [];

		foreach($getSmallId as $k => $smallId)
		{
			
			if(!isset($indexSave[$smallId])) continue; // api didn't give values for this user

			$keys = $indexSave[$smallId];

			if(!isset($keys['steamBans'])) continue;

			$steamInfo = $steamInfos[$keys['steamInfos']];
			$steamBan = $steamBans[$keys['steamBans']];

			/**
			 * Match profile
			 * or create a new class (but don't save yet)
			 * 
			 * Dont break, but move on to next profile
			 * if this one doesnt save for some reason
			 */
			$profile = $profiles->where('small_id', $smallId)->first();

			if(is_null($profile))
			{
				$profile = Profile::firstOrNew(['small_id' => $smallId]);
			}

			if(isset($steamInfo['timecreated']))  $profile->profile_created = $steamInfo['timecreated'];

			$profile->display_name = $steamInfo['personaname'];
			$profile->avatar = Steam::imgToHTTPS($steamInfo['avatarfull']);
			$profile->avatar_thumb = Steam::imgToHTTPS($steamInfo['avatar']);
			$profile->privacy = $steamInfo['communityvisibilitystate'];

			if(!$profile->save()) continue;

			$profileBan = $profileBans->where('profile_id', $profile->id)->first();
			$profileOldAlias = $profileOldAliases->where('profile_id', $profile->id)->all();

			if($this->customList)
			{
				$userDescription = $userDescriptions->where('profile_id', $profile->id)->first();
			}

			/**
			 * Now start inserting profile's ban data if needed by comparing
			 */
			
			/**
			 * Dont update the profile_ban if there is nothing to update
			 * This has to do with in the future when checking for new bans to notify/email
			 */
			$skipProfileBan = false;

			/**
			 * Because this had to be done more manually, check to see if the data
			 * should be updated quietly before calling for ProfileBan
			 */
			$updateTimestamp = true;
			$profileWasUnbanned = false;

			$newVacBanDate = new DateTime();
			$newVacBanDate->sub(new DateInterval("P{$steamBan['DaysSinceLastBan']}D"));

			$combinedBan = (int) $steamBan['NumberOfVACBans'] + (int) $steamBan['NumberOfGameBans'];

			if(!isset($profileBan->id))
			{
				$profileBan = new ProfileBan;
				$profileBan->profile_id = $profile->id;
				$profileBan->unban = false;
			} else {
				$skipProfileBan = $profileBan->skipProfileBanUpdate($steamBan);

				if($profileBan->vac != $combinedBan
				   && $profileBan->vac_banned_on->format('Y-m-d') !== $newVacBanDate->format('Y-m-d'))
				{
					$skipProfileBan = false;
					$profileBan->timestamps = false;
				}
				if($profileBan->vac > $combinedBan)
				{
					$skipProfileBan = false;
					$profileBan->timestamps = false;
					$profileBan->unban = true;
				}
			}

			$profileBan->vac = $combinedBan;
			$profileBan->community = $steamBan['CommunityBanned'];
			$profileBan->trade = $steamBan['EconomyBan'] != 'none';
			$profileBan->vac_banned_on = $newVacBanDate->format('Y-m-d');

			if(!$skipProfileBan) $profile->ProfileBan()->save($profileBan);

			/**
			 * Add current alias to the DB
			 */
			$currentTime = new DateTime();

			if(count($profileOldAlias) == 0)
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
					$newAlias->seen = time();
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

				unset($newAlias);
			}

			$steam64BitId = Steam::to64Bit($profile->small_id);

			$return = [
				'id'				=> $profile->id,
				'display_name'		=> $profile->display_name,
				'avatar'			=> $profile->avatar,
				'avatar_thumb'		=> $profile->avatar_thumb,
				'small_id'			=> $profile->small_id,
				'steam_64_bit'		=> $steam64BitId,
				'steam_32_bit'		=> Steam::to32Bit($steam64BitId),
				'profile_created'	=> isset($profile->profile_created) ? date("M j Y", $profile->profile_created) : "Unknown",
				'privacy'			=> $profile->privacy,
				'alias'				=> Steam::friendlyAlias(json_decode($profile->alias, true)),
				'created_at'		=> $profile->created_at->format("M j Y"),

				'vac'				=> $combinedBan,
				'vac_banned_on'		=> $profileBan->vac_banned_on->format("M j Y"),
				'community'			=> $profileBan->community,
				'trade'				=> $profileBan->trade,

				'site_admin'		=> $profile->site_admin ?: 0,
				'donation'			=> $profile->donation ?: 0,
				'beta'				=> $profile->beta ?: 0,
				'profile_old_alias'	=> array_reverse($oldAliasArray),

				'times_added'		=> [
					'number' => $profile->total,
					'time' => (new DateTime($profile->last_added_created_at))->format("M j Y")
				],
			];

			$this->updateCache($profile->small_id, $return);

			if($this->customList) {
				if($userDescription->profile_name)
				{
					$return['display_name'] = $userDescription->profile_name;
				}

				if($userDescription->profile_description)
				{
					$return['profile_description'] = $userDescription->profile_description;
				}
			}

			$newProfiles[$toSaveKey[$profile->small_id]] = $return;
		}

		// Send somewhere else to update alias
		// This takes too long for many profiles
		$randomString = str_random(12);
		$updateAliasCacheName = "update_alias_";

		if(Cache::has($updateAliasCacheName . $randomString))
			while(Cache::has($updateAliasCacheName . $randomString))
				$randomString = str_random(12);

		Cache::forever($updateAliasCacheName.$randomString, $getSmallId);

		exec('php ' . base_path() . '/artisan update:alias '. $randomString .' > /dev/null 2>/dev/null &');

		return $newProfiles;
	}
}