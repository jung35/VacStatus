<?php namespace VacStatus\Update;

use Cache;

use VacStatus\Steam\SteamAPI;

class MultiProfile
{
	protected $profiles;
	protected $profileCacheName = "profile_";
	protected $refreshProfiles = [];

	function __construct($profiles)
	{
		$this->profiles = $profiles;
	}

	public function run()
	{
		$this->getUpdateAbleProfiles();
		$this->updateUsingAPI();
	}

	private function canUpdate($smallId)
	{
		$cacheName = $this->profileCacheName.$smallId;

		if(Cache::has($cacheName)) return false;

		return true;
	}

	private function getUpdateAbleProfiles()
	{
		$refreshProfiles = [];

		foreach($this->profiles as $k => $profile)
		{
			if(!$this->canUpdate($profile['small_id'])) continue;

			$refreshProfiles[] = [
				'profile_key' => $k,
				'profile' => $profile 
			];
		}

		$this->refreshProfiles = $refreshProfiles;
	}

	private function updateUsingAPI()
	{
		$getSmallId = [];
		foreach($this->refreshProfiles as $profile)
			$getSmallId[] = $profile['profile']['small_id'];

		/* grab 'info' from web api and handle errors */
		$steamAPI = new SteamAPI('info');
		$steamAPI->setSmallId($getSmallId);
		$steamInfo = $steamAPI->run();

		if($steamAPI->error()) return ['error' => $steamAPI->errorMessage()];
		if(!isset($steamInfo->response->players[0])) return ['error' => 'profile_null'];
		// simplify the variable
		$steamInfo = $steamInfo->response->players;

		/* grab 'ban' from web api and handle errors */
		$steamAPI = new SteamAPI('ban');
		$steamAPI->setSmallId($getSmallId);
		$steamBan = $steamAPI->run();

		if($steamAPI->error()) return ['error' => $steamAPI->errorMessage()];
		if(!isset($steamBan->players[0])) return ['error' => 'profile_null'];

		$steamBan = $steamBan->players;



		// Send somewhere else to update alias
		// This takes too long for many profiles
		$randomString = str_random(12);
		$updateAliasCacheName = "update_alias_";

		if(Cache::has($updateAliasCacheName.$randomString))
			while(Cache::has($updateAliasCacheName.$randomString))
				$randomString = str_random(12);

		Cache::forever($updateAliasCacheName.$randomString, $getSmallId);

		shell_exec('php artisan update:alias '. $randomString .' > /dev/null 2>/dev/null &');

		dd($randomString, $getSmallId);
	}
}