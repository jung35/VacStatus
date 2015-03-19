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

	->	Get ALL of the rows from the 'user_list_profile' table
		->	Create a new blank array (call this: ListProfileArray)
		->	For Loop
			-> CHECK IF INDEX OF 'profile_id' IS NOT FOUND IN 'ListProfileArray'
				->	Make value of 'profile_id' as index of 'ListProfileArray' and insert profile detail as array
				->	Along with the profile detail, add another index containing the '# of times profile was added'
					->	start the value with 0
			-> AFTER THE IF STATMENT (NOT ELSE)
				->	Grab the profile from 'ListProfileArray' and add increment
		->	Sort 'ListProfileArray' by '# of times profile was added'
			->	Grab only the first 20 after sorted
				->	The '20' is some nice number I came up with. It could be always changed around
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
			\DB::raw('count(*) as total')
			)->groupBy('profile.id')
			->orderBy('total', 'desc')
			->leftjoin('profile', 'user_list_profile.profile_id', '=', 'profile.id')
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->take(20)
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