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

	public function getList()
	{
		if(!$this->canUpdate())
		{
			$return = $this->grabCache();
			if($return !== false) return $return;
		}

		$multiProfile = new MultiProfile($this->grabFromDB());
		$return = $multiProfile->run();

		$this->updateCache($return);

		return $return;
	}

	protected function grabFromDB()
	{
		return UserListProfile::orderBy('total', 'desc')
			->getProfiles();
	}
}