<?php namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;
use VacStatus\Update\MultiProfile;

use Cache;
use Carbon;
use DateTime;

use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

/*

	STEPS TO GET LATEST TRACKED USERS

*************************************************************************************************

	->	Grab 20 rows from the 'user_list_profile' table
		->	Order By DESC
		->	The '20' is some nice number I came up with. It could be always changed around
*/

/*

	RETURN FORMAT

*************************************************************************************************

	return [
		[ (There should be many of these) (SORTED BY DESC) (NO SPECIFIC INDEX VALUE)
			profile.id
			profile.display_name
			profile.avatar_thumb

			profile_ban.vac
				-> this is the number of vac bans
			profile_ban.vac_banned_on
				-> see to convert date
					https://github.com/jung3o/VacStatus/tree/c6e626d8f8ab5f8c99db80f904275c185698c645/app/models/Profile.php#L131
			profile_ban.community
			profile_ban.trade

			users.site_admin
				-> color name (class: .admin-name)
			users.donation
				-> color name (class: .donator-name)
			users.beta
				-> color name (class: .beta-name)
		]
	]

*/

class LatestTracked extends BaseUpdate
{
	function __construct()
	{
		$this->cacheLength = 1;
		$this->cacheName = "latestTracked";
	}

	public function getLatestTracked()
	{
		if(!$this->canUpdate()) {
			$return = $this->grabCache();
			if($return !== false) return $return;
		}

		return $this->grabFromDB();
	}

	private function grabFromDB()
	{
		$userListProfiles = UserListProfile::orderBy('user_list_profile.id', 'desc')
			->take(20)
			->leftjoin('profile', 'user_list_profile.profile_id', '=', 'profile.id')
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
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
				\DB::raw('count(*) as total'),
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

		foreach($userListProfiles as $k => $userListProfile)
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