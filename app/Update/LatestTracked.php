<?php

namespace VacStatus\Update;

use VacStatus\Models\UserListProfile;

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