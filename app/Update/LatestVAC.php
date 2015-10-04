<?php

namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;
use VacStatus\Update\MultiProfile;

use Cache;
use Carbon;
use DateTime;

use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

class LatestVAC extends MostTracked
{
	function __construct()
	{
		$this->cacheLength = 30;
		$this->cacheName = "latestVAC";
	}

	protected function grabFromDB()
	{
		return UserListProfile::orderBy('profile_ban.last_ban_date', 'desc')
			->where('profile_ban.vac_bans', '>', '0')
			->whereNotNull('profile_ban.last_ban_date')
			->getProfiles(200);
	}
}