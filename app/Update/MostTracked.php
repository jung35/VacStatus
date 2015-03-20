<?php namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;

use Cache;
use Carbon;
use DateTime;

use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

/*

	STEPS TO GET MOST TRACKED USERS

*************************************************************************************************

	->	sql
	
*/

/*

	RETURN FORMAT

*************************************************************************************************

	return [
		user_list_profile.profile_id => [ (There should be many of these)
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



class MostTracked extends BaseUpdate
{
	function __construct()
	{
		$this->cacheName = "mostTracked";
	}

	public function getMostTracked()
	{
		if(!$this->canUpdate()) {
			$return = $this->grabCache();
			if($return !== false) return $return;
		}

		return $this->grabFromDB();
	}

	private function grabFromDB()
	{
		$userListProfiles = UserListProfile::select(
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
			\DB::raw('count(user_list_profile.id) as total')
			)->groupBy('profile.id')
			->orderBy('total', 'desc')
			->whereNull('user_list_profile.deleted_at')
			->leftjoin('profile', 'user_list_profile.profile_id', '=', 'profile.id')
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->take(20)
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