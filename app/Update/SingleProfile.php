<?php namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;

use VacStatus\Models\Profile;

use Cache;
use Carbon;

class SingleProfile extends BaseUpdate
{
	protected $profileId;

	function __construct($profileId)
	{
		$this->profileId = $profileId;
		$this->cacheName = "profile_".$profileId;
	}

	private function getProfile()
	{
		// if(!$this->canUpdate()) return Profile::whereId($this->profileId)->first();
	}
}

/*

	STEPS TO GET PROFILE

*************************************************************************************************

	->	Check if the cache has expired

	->	REQUIRES UPDATE
		->	Request to STEAM WEB API
			->	'info'	->	reponse.players[0]
				->	profile.small_id 			->	toSmallId	->	"steamid"
				->	profile.privacy 			->				->	"communityvisibilitystate"
				->	profile.display_name		->				->	"personaname"
				->	profile.avatar 				->	see: [0]	->	"avatarfull"
				->	profile.avatar_thumb 		->	see: [0]	->	"avatar"
				->	profile.profile_created 	->	null(?)		->	"timecreated"
				->	profile_old_alias			-> (ADD DISPLAY NAME ONLY IF IT'S UNIQUE)
			->	'ban'	->	players[0]
				(MAKE SURE TO CHECK IF THE VALUE HAVE BEEN CHANGED BEFORE RETURNING)
				->	profile_ban.unban			->	COMPARE profile.vac AND "NumberOfVACBans"
				->	profile_ban.community 		->				->	"CommunityBanned"
				->	profile_ban.vac 			->				->	"NumberOfVACBans"
				->	profile_ban.vac_banned_on	->	see: [1]	->	"DaysSinceLastBan"
				->	profile_ban.trade 			->				->	"EconomyBan"
			->	'alias'
				(THIS ONE IS A VERY UNSTABLE API SO DON'T DIE WHEN IT DOESNT RESPOND)
				-> profile.alias 				->	see: [2]	->	(ALL OF IT)
		->	ADD ALL OF THE VALUES INTO AN ARRAY
			->	USE "RETURN FORMAT" AS REFERENCE

	->	NO UPDATE
		->	DO A QUERY USING LEFT JOIN TO MAKE IT EFFICIENT AS POSSIBLE
			-> see: [3]
		->	MOVE THE VALUES FROM QUERY TO A NEW ARRAY
			->	USE "RETURN FORMAT" AS REFERENCE

	[0]: https://github.com/jung3o/VacStatus/blob/master/app/models/Profile.php#L97
	[1]: https://github.com/jung3o/VacStatus/blob/master/app/models/Profile.php#L115
	[2]: https://github.com/jung3o/VacStatus/blob/master/app/models/Profile.php#L105
	[3]: https://github.com/jung3o/VacStatus/blob/master/app/models/Profile.php#L187

 */

/*

	RETURN FORMAT

*************************************************************************************************

	profile = [
		profile.display_name
		profile.avatar
			-> this is the bigger one. other one is avatar_thumb
		profile.small_id
			-> make STEAM3ID by adding U:1: before the small_id
			-> convert to 32bit & 64bit ID
		profile.profile_created (CAN BE NULL)
			-> private profiles are NULL (UNLESS WE ALREADY HAD THEIR DATE)
		profile.privacy
			-> 1 - Private
			-> 2 - Friends only
			-> 3 - Public
		profile.alias
			-> convert from JSON to ARRAY
				json_encode($value)
			-> sort by time
				https://github.com/jung3o/VacStatus/blob/master/app%2FSteam%2FSteamUser.php#L13
			-> conver time
				https://github.com/jung3o/VacStatus/blob/master/app%2FSteam%2FSteamUser.php#L26
		profile.created_at
		profile_ban.vac
			-> this is the number of vac bans
		profile_ban.vac_banned_on
			-> see to convert date
				https://github.com/jung3o/VacStatus/blob/master/app/models/Profile.php#L131
		profile_ban.community
		profile_ban.trade
		users.site_admin
			-> badge (class: .label.label-warning)
			-> color name (class: .admin-name)
		users.donation
			-> badge (class: .label.label-success)
			-> color name (class: .donator-name)
		users.beta
			-> badge (class: .label.label-primary)
			-> color name (class: .beta-name)
		profile_old_alias = [
			profile_old_alias.seen
				-> this is a UNIX timestamp
				-> convert UNIX timestamp to readable DATE ("M j Y, g:i a")
					ex. Mar 0 2015, 10:57 am
			profile_old_alias.seen_alias
		]
		TIMES_CHECKED = [ (FROM CACHE)
			NUMBER OF TIMES CHECKED
			TIMESTAMP - UNIX
		]
		TIMES_ADDED = [ (FROM CACHE)
			NUMBER OF TIMES ADDED
			TIMESTAMP - UNIX
		]

	]

 */