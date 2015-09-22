<?php namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;
use VacStatus\Update\MultiProfile;

use Cache;
use Carbon;
use DateTime;

use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

/*
 * This is almost an exact copy of LatestTracked.php. The only thing that's different is
 * I am now filtering where last_ban_date IS NOT NULL AND vac > 0 ORDER BY last_ban_date DESC
 */

class LatestGameBan extends BaseUpdate
{
	function __construct()
	{
		$this->cacheLength = 30;
		$this->cacheName = "latestGameBan";
	}

	public function getLatestGameBan()
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
		$userListProfiles = UserListProfile::orderBy('profile_ban.last_ban_date', 'desc')
			->where('profile_ban.game_bans', '>', '0')
			->whereNotNull('profile_ban.last_ban_date')
			->getProfiles(200);

		$multiProfile = new MultiProfile($userListProfiles);
		$return = $multiProfile->run();

		$this->updateCache($return);
		
		return $return;
	}
}