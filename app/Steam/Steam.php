<?php namespace VacStatus\Steam;

use Cache;

class Steam {
	/**
	 * Minimum timeout for steam updates on profile
	 * @var integer
	 */
	public static $UPDATE_TIME = 3600; // 1 HOUR = 3600 seconds
	protected static $HTTPS_URL = 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/';

	public static function getAPI()
	{
		return env('STEAM_API');
	}

	public static function canUpdate($smallId)
	{
		if(Cache::has("profile_$smallId")) {
			if(Cache::get("profile_$smallId") + self::$UPDATE_TIME > time()) {
				return false;
			}
		}
		return true;
	}

	public static function setUpdate($smallId)
	{
		Cache::put("profile_$smallId", time(), self::$UPDATE_TIME / 60);
		return;
	}

	public static function toSmallId($steam64BitId)
	{
		if(is_array($steam64BitId))
		{
			$smallIds = [];
			foreach($steam64BitId as $key => $value)
			{
				$smallIds[$key] = explode('.', bcsub($value,'76561197960265728'))[0];
			}

			return $smallIds;
		}

		if(is_numeric($steam64BitId))
		{
			$steam64BitId .= '';
			return explode('.', bcsub($steam64BitId,'76561197960265728'))[0];
		}

		return ['type' => 'error'];
	}

	public static function to64bit($smallId)
	{
		if(is_array($smallId))
		{
			$steam64BitIds = [];
			foreach($smallId as $key => $value)
			{
				$steam64BitIds[$key] = explode('.', bcadd($value,'76561197960265728'))[0];
			}

			return $steam64BitIds;
		}

		if(is_numeric($smallId))
		{
			$smallId .= '';
			return explode('.', bcadd($smallId,'76561197960265728'))[0];
		}

		return ['type' => 'error'];
	}

	public static function to32Bit($steam64BitId)
	{
		if(is_numeric($steam64BitId))
		{
			$steamIdPartOne = substr($steam64BitId, -1) % 2 == 0 ? 0 : 1;
			$steamIdPartTwo = bcsub($steam64BitId, '76561197960265728');

			if (bccomp($steamIdPartTwo,'0') == 1)
			{
				$steamIdPartTwo = bcsub($steamIdPartTwo, $steamIdPartOne);
				$steamIdPartTwo = bcdiv($steamIdPartTwo, 2);

				return "STEAM_0:$steamIdPartOne:".explode('.', $steamIdPartTwo)[0];
			}
		}

		return ['type' => 'error'];
	}

	public static function toSteam3Id($steam64BitId)
	{
		if(is_numeric($steam64BitId))
		{
			return 'U:1:'.self::toSmallId($steam64BitId);
		}

		return ['type' => 'error'];
	}

	public static function imgToHTTPS($url)
	{
		preg_match('/^(.*)?\/avatars\/(.*)$/i', $url, $newURL);

		return self::$HTTPS_URL.$newURL[2];
	}

	public static function aliasSort($a, $b) {
		return strcmp(self::aliasTimeConvert($b->timechanged), self::aliasTimeConvert($a->timechanged));
	}

	public static function aliasTimeConvert($time) {
		return strtotime(str_replace("@", "", $time));
	}

	public static function friendlyAlias($aliases)
	{
		$newAlias = [];

		foreach($aliases as $alias)
		{
    		$newAlias[] = [
    			'newname' => $alias->newname,
    			'timechanged' => date('M j Y', strtotime(str_replace("@", "", $alias->timechanged)))
			];
		}

		return $newAlias;
	}
}