<?php namespace VacStatus\Steam;

use VacStatus\Steam\Steam;
use Cache;
use Carbon;

use GuzzleHttp\Client;

class SteamAPI {

	protected $steamURL = [
		'info'		=> 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/',
		'friends'	=> 'http://api.steampowered.com/ISteamUser/GetFriendList/v0001/',
		'ban'		=> 'http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/',
		'vanityUrl'	=> 'http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/',
		'alias'		=> 'http://steamcommunity.com/profiles/{steam64BitId}/ajaxaliases'
	];

	private $steam64BitId;

	function __construct($steamId, $isSmallId = false)
	{
		if($isSmallId) $steamId = Steam::to64Bit($steamId);
		if(is_array($steamId)) $steamId = implode(',', $steamId);

		$this->steam64BitId = $steamId;
	}

	private function getUser($type)
	{
		$steam64BitId = $this->steam64BitId;

		switch($type)
		{
			case 'friends':
				return "&relationship=friend&steamid={$steam64BitId}";
			case 'vanityUrl':
				return "&vanityurl={$steam64BitId}";
			default:
				return "&steamids={$steam64BitId}";
		}

		return false;
	}

	private function getKey()
	{
		return "?key=" . Steam::getAPI();
	}

	private function error($reason)
	{
		return [ 'error' => $reason ];
	}

	private function setCache()
	{
		$cache_name = 'steamAPICalls';
		$expiresAt = Carbon::today()->addDay();

		if(!Cache::has($cache_name)) Cache::put($cache_name, 0, $expiresAt);

		Cache::increment($cache_name);
	}

	public function fetch($type)
	{
		$this->setCache();

		if(!isset($this->steamURL[$type])) return $this->error('invalid_type');

		if($type !== 'alias')
		{
			$url = $this->steamURL[$type] . $this->getKey() . $this->getUser($type);
		} else {
			$url = preg_replace('/\{steam64BitId\}/', $this->steam64BitId, $this->steamURL[$type]);
		}

		try {
			$client = new Client();
			$request = $client->get($url, ['http_errors' => false]);
		}
		catch(\Exception $e)
		{
			return $this->error('api_conn_err');
		}

		$data = json_decode($request->getBody(), true);

		if(!is_array($data)) return $this->error('api_data_err');

		return $data;
	}
}
