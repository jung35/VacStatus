<?php

namespace VacStatus\Update;

use VacStatus\Models\UserListProfile;

class MostTracked extends BaseUpdate
{
	function __construct()
	{
		$this->cacheName = "mostTracked";
	}

	public function getList()
	{
		$return = $this->grabCache();
		if($return !== false) return $return;

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