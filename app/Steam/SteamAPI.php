<?php namespace VacStatus\Steam;

use VacStatus\Steam\Steam;
use Cache;

class SteamAPI {

	protected $url;
	protected $type;
	protected $steamURL = [
		'info'		=> 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/',
		'friends'	=> 'http://api.steampowered.com/ISteamUser/GetFriendList/v0001/',
		'ban'		=> 'http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/',
		'vanityUrl'	=> 'http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/',
		'alias'		=> 'http://steamcommunity.com/profiles/{steam64BitId}/ajaxaliases'
	];

	function __construct($type)
	{
		$this->type = $type;
		$this->url = $this->steamURL[$type];

		if($type != 'alias')
		{
			$this->url .= "?key=".Steam::getAPI();
		}

		if($type == 'friends')
		{
			$this->url .= "&relationship=friend";
		}
	}

	public function setSteamId($steam64BitId)
	{
		if(is_array($steam64BitId))
		{
			$steam64BitId = implode(',', $steam64BitId);
		}

		switch($this->type)
		{
			case 'friends':
				$this->url .= "&steamid=$steam64BitId";
				break;
			case 'vanityUrl':
				$this->url .= "&vanityurl=$steam64BitId";
				break;
			case 'alias':
				$this->url = preg_replace('/\{steam64BitId\}/', $steam64BitId, $this->url);
				break;
			default:
				$this->url .= "&steamids=$steam64BitId";
				break;
		}
	}

	public function run()
	{
		$cache_name = 'steamAPICalls_'.(date('M_j_Y'));
		if(!Cache::has($cache_name))
		{
			Cache::forever($cache_name, 0);
		}

		Cache::forever($cache_name, Cache::get($cache_name)+1);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		try {
			$data = curl_exec($ch);
		} catch(Exception $e) {
			return (object) [
				'type' => 'error',
				'data' => 'api_conn_err'
			];
		}

		curl_close($ch);
		$data = json_decode($data);

		if(!is_object($data) && !is_array($data))
		{
			return (object) [
				'type' => 'error',
				'data' => 'api_data_err'
			];
		}

		return $data;
	}

	public function addVanity($steam64BitId)
	{
		$this->url .= "&vanityurl=$steam64BitId";
	}

	public static function cURLSteamAPI($type = null, $value = null, $try = true) {
	// Maybe it should have default type...?
	if($type == null || $value == null) return false;
	$cache_name = 'steamAPICalls_'.(date('M_j_Y'));
	if(!Cache::has($cache_name)) {
	  Cache::forever($cache_name, 0);
	}
	Cache::forever($cache_name, Cache::get($cache_name)+1);
	$steamAPI = self::getAPI();
	// So this url doesn't float in some files as many different url's
	// keeping them in one place
	switch($type) {
	  // Get most of all public information about this steam user
	  case 'info':
		if(is_array($value)) {
		  $value = implode(',', $value);
		}
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$steamAPI}&steamids={$value}&".time();
		break;
	  // Get list of friends (Profile must not be private)
	  case 'friends':
		if(is_array($value)) {
		  $value = $value[0];
		}
		$url = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key={$steamAPI}&steamid={$value}&relationship=friend&".time();
		break;
	  // Get more detailed information about this person's ban status
	  case 'ban':
		if(is_array($value)) {
		  $value = implode(',', $value);
		}
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key={$steamAPI}&steamids={$value}&".time();
		break;
	  // Get list of usernames this user has used
	  case 'alias':
		if(is_array($value)) {
		  $value = $value[0];
		}
		$url = "http://steamcommunity.com/profiles/{$value}/ajaxaliases?".time();
		break;
	  // For checking to make sure a user exists by this profile name
	  case 'vanityUrl':
		if(is_array($value)) {
		  $value = $value[0];
		}
		$url = "http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key={$steamAPI}&vanityurl={$value}&".time();
		break;
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 7);
	try {
	  $data = curl_exec($ch);
	} catch(Exception $e) {
	  if($try) {
		return self::cURLSteamAPI($type, $value, false);
	  }
	  return (object) array('type' => 'error',
							'data' => 'api_conn_err');
	}
	curl_close($ch);
	$data = json_decode($data);
	if(!is_object($data) && !is_array($data)) {
	  return (object) array('type' => 'error',
							'data' => 'api_data_err');
	}
	return $data;
  }
}