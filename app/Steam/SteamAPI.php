<?php namespace VacStatus\Steam;

use VacStatus\Steam\Steam;
use Cache;
use Carbon;

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

	private $error = false;
	private $errorMessage = '';

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

	public function setSmallId($smallId)
	{
		$this->setSteamId(Steam::to64Bit($smallId));
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
		$cache_name = 'steamAPICalls';
		$expiresAt = Carbon::today()->addDay();

		if(!Cache::has($cache_name)) Cache::put($cache_name, 0, $expiresAt);

		Cache::increment($cache_name);

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
			$this->error = true;
			$this->errorMessage = 'api_conn_err';

			return (object) [
				'type' => 'error',
				'data' => $this->errorMessage
			];
		}

		curl_close($ch);
		$data = json_decode($data);

		if(!is_object($data) && !is_array($data))
		{
			$this->error = true;
			$this->errorMessage = 'api_data_err';

			return (object) [
				'type' => 'error',
				'data' => $this->errorMessage
			];
		}

		return $data;
	}

	public function error()
	{
		return $this->error;
	}

	public function errorMessage()
	{
		return $this->errorMessage;
	}
}