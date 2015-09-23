<?php

namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;
use VacStatus\Update\MultiProfile;

use Cache;
use Carbon;
use DateTime;

use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

class LatestTracked extends BaseUpdate
{
	function __construct()
	{
		$this->cacheLength = 30;
		$this->cacheName = "latestTracked";
	}

	public function getLatestTracked()
	{
		if(!$this->canUpdate())
		{
			$return = $this->grabCache();
			if($return !== false) return $return;
		}

		return $this->grabFromDB();
	}

	private function grabFromDB()
	{
		$userListProfiles = UserListProfile::orderBy('user_list_profile.id', 'desc')
			->getProfiles();

		$multiProfile = new MultiProfile($userListProfiles);
		$return = $multiProfile->run();

		$this->updateCache($return);
		
		return $return;
	}
}