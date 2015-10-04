<?php

namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;
use VacStatus\Update\MultiProfile;

use Cache;
use Carbon;
use DateTime;

use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

class LatestTracked extends MostTracked
{
	function __construct()
	{
		$this->cacheLength = 30;
		$this->cacheName = "latestTracked";
	}

	protected function grabFromDB()
	{
		return UserListProfile::orderBy('user_list_profile.id', 'desc')
			->getProfiles();
	}
}