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
		if(!$this->canUpdate()) return Profile::whereId($this->profileId)->first();
	}
}

/**

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
		profile_ban.vac
			-> this is the number of vac bans
		profile_ban.vac_banned_on
			-> see to convert date
				https://github.com/jung3o/VacStatus/blob/master/app/models/Profile.php#L115
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

	]

 **/