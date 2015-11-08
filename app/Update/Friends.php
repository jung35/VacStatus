<?php

namespace VacStatus\Update;

use VacStatus\Models\UserListProfile;
use Auth;

class Friends extends MostTracked
{
	function __construct()
	{
		$this->cacheLength = 30;
		$this->user = Auth::user();
		$this->cacheName = "friends_{$this->user->id}";
	}

	protected function grabFromDB()
	{
		$friendsList = json_decode($this->user->friendslist);

		$tempProfile = array_map(function($smallId) {
			return ['small_id' => $smallId];
		}, $friendsList);

		$multiProfile = new MultiProfile($tempProfile);

		return $multiProfile->run();
	}
}