<?php

namespace VacStatus\Update;

use VacStatus\Models\UserListProfile;

class LatestGameBan extends MostTracked
{
	function __construct()
	{
		$this->cacheLength = 30;
		$this->cacheName = "latestGameBan";
	}

	protected function grabFromDB()
	{
		return UserListProfile::orderBy('profile_ban.last_ban_date', 'desc')
			->where('profile_ban.game_bans', '>', '0')
			->whereNotNull('profile_ban.last_ban_date')
			->getProfiles(200);
	}
}