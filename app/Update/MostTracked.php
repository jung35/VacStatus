<?php

namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;

use Cache;
use Carbon;
use DateTime;

use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

class MostTracked extends BaseUpdate
{
	function __construct()
	{
		$this->cacheName = "mostTracked";
	}

	public function getMostTracked()
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
		$userListProfiles = UserListProfile::orderBy('total', 'desc')
			->getProfiles();

		$multiProfile = new MultiProfile($userListProfiles);
		$return = $multiProfile->run();

		$this->updateCache($return);

		return $return;
	}
}