<?php namespace VacStatus\Update;

use Cache;
use Carbon;
use DateTime;
use DateInterval;

use VacStatus\Steam\Steam;
use VacStatus\Steam\SteamAPI;

use VacStatus\Models\Profile;
use VacStatus\Models\ProfileOldAlias;
use VacStatus\Models\UserListProfile;

class MultiProfile
{
	protected $profiles;
	protected $profileCacheName = "profile_";
	protected $cacheLength = 1;
	protected $refreshProfiles = [];

	function __construct($profiles)
	{
		$this->profiles = $profiles;
	}

	public function run()
	{
		$this->getUpdateAbleProfiles();
		$updatedProfiles = $this->updateUsingAPI();

		if(isset($updatedProfiles['error']))
		{
			if($updatedProfiles['error'] == 'profile_null'){
				$updatedProfiles = [];
			} else {
				return ['error' => $updatedProfiles['error']];
			}
		}

		return array_replace($this->profiles, $updatedProfiles);
	}

	private function canUpdate($smallId)
	{
		$cacheName = $this->profileCacheName.$smallId;

		if(Cache::has($cacheName)) return false;

		return true;
	}

	protected function updateCache($smallId, $data)
	{
		unset($data['times_checked']);
		unset($data['times_added']);
		unset($data['login_check']);

		$cacheName = $this->profileCacheName.$smallId;
		if(Cache::has($cacheName)) Cache::forget($cacheName);

		$expireTime = Carbon::now()->addMinutes($this->cacheLength);

		Cache::put($cacheName, $data, $expireTime);
	}

	private function getUpdateAbleProfiles()
	{
		$refreshProfiles = [];

		foreach($this->profiles as $k => $profile)
		{
			if(!$this->canUpdate($profile['small_id'])) continue;

			$refreshProfiles[] = [
				'profile_key' => $k,
				'profile' => $profile 
			];
		}

		$this->refreshProfiles = $refreshProfiles;
	}

	private function updateUsingAPI()
	{
		$getSmallId = [];
		foreach($this->refreshProfiles as $profile)
		{
			$smallId = $profile['profile']['small_id'];
			$key = $profile['profile_key'];

			$getSmallId[] = $smallId;
			$toSaveKey[$smallId] = $key;
		}

		/* grab 'info' from web api and handle errors */
		$steamAPI = new SteamAPI('info');
		$steamAPI->setSmallId($getSmallId);
		$steamInfos = $steamAPI->run();

		if($steamAPI->error()) return ['error' => $steamAPI->errorMessage()];
		if(!isset($steamInfos->response->players[0])) return ['error' => 'profile_null'];
		// simplify the variable
		$steamInfos = $steamInfos->response->players;

		/* grab 'ban' from web api and handle errors */
		$steamAPI = new SteamAPI('ban');
		$steamAPI->setSmallId($getSmallId);
		$steamBans = $steamAPI->run();

		if($steamAPI->error()) return ['error' => $steamAPI->errorMessage()];
		if(!isset($steamBans->players[0])) return ['error' => 'profile_null'];

		$steamBans = $steamBans->players;

		$profiles = Profile::whereIn('profile.small_id', $getSmallId)
			->leftjoin('profile_ban', 'profile_ban.profile_id', '=', 'profile.id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			// ->select()
			->get([
				'profile.*',
				'profile_ban.community',
				'profile_ban.vac',
				'profile_ban.trade',
				'profile_ban.unban',
				'users.site_admin',
				'users.donation',
				'users.beta'
			]);

		// dd($profiles);
		// dd($profiles->where('small_id', 9856)->first());
		$profileIds = [];

		foreach($profiles as $profile)
		{
			$profileIds[] = $profile->id;
		}

		$userListProfile = UserListProfile::whereIn('profile_id', $profileIds)
			->orderBy('id','desc')
			->get();

		$indexSave = [];

		foreach($steamInfos as $k => $info)
		{
			$indexSave[Steam::toSmallId($info->steamid)] = ['steamInfos' => $k];
		}

		foreach($steamBans as $k => $ban)
		{
			// Lets just not update if api didn't return for this user
			if(!isset($indexSave[Steam::toSmallId($ban->SteamId)])) continue;
			$indexSave[Steam::toSmallId($ban->SteamId)]['steamBans'] = $k;
		}

		$newProfiles = [];

		foreach($getSmallId as $k => $smallId)
		{
			// api didn't give values for this user
			if(!isset($indexSave[$smallId])) continue;

			$keys = $indexSave[$smallId];

			$steamInfo = $steamInfos[$keys['steamInfos']];
			$steamBan = $steamBans[$keys['steamBans']];

			$profile = $profiles->where('small_id', $smallId)->first();

			if(is_null($profile))
			{
				$profile = new Profile;
				$profile->small_id = $this->smallId;

				if(isset($steamInfo->timecreated)) // people like to hide their info because smurf or hack
				{
					$profile->profile_created = $steamInfo->timecreated;
				}
			}

			$profile->display_name = $steamInfo->personaname;
			$profile->avatar = Steam::imgToHTTPS($steamInfo->avatarfull);
			$profile->avatar_thumb = Steam::imgToHTTPS($steamInfo->avatar);
			$profile->privacy = $steamInfo->communityvisibilitystate;

			$profile->save();

			$profileBan = $profile->ProfileBan;

			// Dont update the profile_ban if there is nothing to update
			// This has to do with in the future when I check for new bans to notify/email
			$skipProfileBan = false;

			$newVacBanDate = new DateTime();
			$newVacBanDate->sub(new DateInterval("P{$steamBan->DaysSinceLastBan}D"));

			if(!isset($profileBan->id))
			{
				$profileBan = new ProfileBan;
				$profileBan->profile_id = $profile->id;
				$profileBan->unban = false;
			} else {
				$skipProfileBan = $profileBan->skipProfileBanUpdate($steamBan);

				if($profileBan->vac > $steamBan->NumberOfVACBans)
				{
				  $profileBan->unban = true;
				}
			}
			$profileBan->vac = $steamBan->NumberOfVACBans;
			$profileBan->community = $steamBan->CommunityBanned;
			$profileBan->trade = $steamBan->EconomyBan != 'none';
			$profileBan->vac_banned_on = $newVacBanDate->format('Y-m-d');

			if(!$skipProfileBan) $profile->ProfileBan()->save($profileBan);

			/* Time to do profile_old_alias */
			/* Checks to make sure if there is already a same name before inserting new name */
			$profileOldAlias = $profile->ProfileOldAlias()->whereProfileId($profile->id)->orderBy('id','desc')->get();

			if($profileOldAlias->count() == 0)
			{
				$profileOldAlias = new ProfileOldAlias;
				$profileOldAlias->profile_id = $profile->id;
				$profileOldAlias->seen = time();
				$profileOldAlias->seen_alias = $profile->display_name;
				$profileOldAlias->save();
			} else {
				$match = false;
				$recent = 0;
				foreach($profileOldAlias as $oldAlias)
				{
					if(is_object($oldAlias))
					{
						if($oldAlias->seen_alias == $profile->display_name)
						{
							$match = true;
							break;
						}

						$recent = $oldAlias->compareTime($recent);
					}
				}

				if(!$match && $recent + Steam::$UPDATE_TIME < time())
				{
					$newAlias = new ProfileOldAlias;
					$newAlias->profile_id = $profile->id;
					$newAlias->seen = time();
					$newAlias->seen_alias = $profile->display_name;
					$profile->ProfileOldAlias()->save($newAlias);
				}
			}

			$steam64BitId = Steam::to64Bit($profile->small_id);

			$vacBanDate = new DateTime();
			$vacBanDate->sub(new DateInterval("P{$steamBan->DaysSinceLastBan}D"));

			$oldAliasArray = [];

			foreach($profileOldAlias as $k => $oldAlias)
			{
				$oldAliasArray[] = [
					"newname" => $oldAlias->seen_alias,
					"timechanged" => $oldAlias->seen->format("M j Y")
				];
			}

			$gettingCount = $userListProfile->where('profile_id', $profile->id);

			$profileTimesAdded = [
				'number' => $gettingCount->count(),
				'time' => isset($gettingCount[0]) ? (new DateTime($gettingCount[0]->created_at))->format("M j Y") : null
			];

			$profileCheckCache = "profile_checked_";

			$currentProfileCheck = [
				'number' => 0,
				'time' => date("M j Y", time())
			];

			if(Cache::has($profileCheckCache.$profile->smallId))
			{
				$currentProfileCheck = Cache::get($profileCheckCache.$profile->smallId);
			}

			$newProfileCheck = [
				'number' => $currentProfileCheck['number'] + 1,
				'time' => date("M j Y", time())
			];

			Cache::forever($profileCheckCache.$profile->smallId, $newProfileCheck);

			$return = [
				'id'				=> $profile->id,
				'display_name'		=> $steamInfo->personaname,
				'avatar'			=> Steam::imgToHTTPS($steamInfo->avatarfull),
				'avatar_thumb'		=> Steam::imgToHTTPS($steamInfo->avatar),
				'small_id'			=> $profile->small_id,
				'steam_64_bit'		=> $steam64BitId,
				'steam_32_bit'		=> Steam::to32Bit($steam64BitId),
				'profile_created'	=> isset($steamInfo->timecreated) ? date("M j Y", $steamInfo->timecreated) : "Unknown",
				'privacy'			=> $steamInfo->communityvisibilitystate,
				'alias'				=> $profile->alias,
				'created_at'		=> $profile->created_at->format("M j Y"),
				'vac'				=> $steamBan->NumberOfVACBans,
				'vac_banned_on'		=> $vacBanDate->format("M j Y"),
				'community'			=> $steamBan->CommunityBanned,
				'trade'				=> $steamBan->EconomyBan != 'none',
				'site_admin'		=> $profile->site_admin?:0,
				'donation'			=> $profile->donation?:0,
				'beta'				=> $profile->beta?:0,
				'profile_old_alias'	=> $oldAliasArray,
				'times_checked'		=> $currentProfileCheck,
				'times_added'		=> $profileTimesAdded,
			];

			$newProfiles[$toSaveKey[$profile->small_id]] = $return;

			$this->updateCache($profile->small_id, $return);
		}

		// Send somewhere else to update alias
		// This takes too long for many profiles
		$randomString = str_random(12);
		$updateAliasCacheName = "update_alias_";

		if(Cache::has($updateAliasCacheName.$randomString))
			while(Cache::has($updateAliasCacheName.$randomString))
				$randomString = str_random(12);

		Cache::forever($updateAliasCacheName.$randomString, $getSmallId);

		shell_exec('php artisan update:alias '. $randomString .' > /dev/null 2>/dev/null &');

		return $newProfiles;
	}
}