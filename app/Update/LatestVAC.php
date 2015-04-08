<?php namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;
use VacStatus\Update\MultiProfile;

use Cache;
use Carbon;
use DateTime;

use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

/*
 * This is almost an exact copy of LatestTracked.php. The only thing that's different is
 * I am now filtering where vac_banned_on IS NOT NULL AND vac > 0 ORDER BY vac_banned_on DESC
 */

class LatestVAC extends BaseUpdate
{
	function __construct()
	{
		$this->cacheLength = 30;
		$this->cacheName = "latestVAC";
	}

	public function getLatestVAC()
	{
		// if(!$this->canUpdate()) {
		// 	$return = $this->grabCache();
		// 	if($return !== false) return $return;
		// }

		return $this->grabFromDB();
	}

	private function grabFromDB()
	{
		$userListProfiles = UserListProfile::orderBy('user_list_profile.id', 'desc')
			->take(20)
			->leftjoin('profile', 'user_list_profile.profile_id', '=', 'profile.id')
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->whereNull('user_list_profile.deleted_at')
			->whereNotNull('profile_ban.vac_banned_on')
			->where('profile_ban.vac', '>', '0')
			->orderBy('profile_ban.vac_banned_on', 'DESC')
			->groupBy('profile.id')
			->get([
				'profile.id',
				'profile.display_name',
				'profile.avatar_thumb',
				'profile.small_id',

				'profile_ban.vac',
				'profile_ban.vac_banned_on',
				'profile_ban.community',
				'profile_ban.trade',

				'users.site_admin',
				'users.donation',
				'users.beta',

				\DB::raw('max(user_list_profile.created_at) as created_at'),
				\DB::raw('count(user_list_profile.id) as total'),
			]);

		$profileIds = [];
		foreach($userListProfiles as $userListProfile)
		{
			$profileIds[] = $userListProfile->id;
		}

		$all = UserListProfile::whereIn('profile_id', $profileIds)
			->orderBy('id', 'desc')
			->get();

		$return = [];

		foreach($userListProfiles as $userListProfile)
		{
			$vacBanDate = new DateTime($userListProfile->vac_banned_on);

			$return[] = [
				'id'			=> $userListProfile->id,
				'display_name'	=> $userListProfile->display_name,
				'avatar_thumb'	=> $userListProfile->avatar_thumb,
				'small_id'		=> $userListProfile->small_id,
				'steam_64_bit'	=> Steam::to64Bit($userListProfile->small_id),
				'vac'			=> $userListProfile->vac,
				'vac_banned_on'	=> $vacBanDate->format("M j Y"),
				'community'		=> $userListProfile->community,
				'trade'			=> $userListProfile->trade,
				'site_admin'	=> $userListProfile->site_admin?:0,
				'donation'		=> $userListProfile->donation?:0,
				'beta'			=> $userListProfile->beta?:0,
				'times_added'	=> [
					'number' => $userListProfile->total,
					'time' => (new DateTime($userListProfile->created_at))->format("M j Y")
				],
			];
		}

		$multiProfile = new MultiProfile($return);
		$return = $multiProfile->run();

		$this->updateCache($return);
		
		return $return;
	}
}