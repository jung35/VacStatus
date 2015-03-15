<?php namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;

use Cache;
use Carbon;

/*

	STEPS TO GET MOST TRACKED USERS

*************************************************************************************************

	->	Get ALL of the rows from the 'user_list_profile' table
		->	Create a new blank array (call this: ListProfileArray)
		->	Loop
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
	function __constructor()
	{
		$this->cacheName = "mostTracked";
	}

	public function getMostTracked()
	{
		if(!$this->canUpdate()) return Cache::get($this->cacheName);

		// do update stuff thingy
	}
}