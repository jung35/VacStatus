<?php

namespace VacStatus\Steam;

use Cache;

class Steam {

	/**
	 * Minimum timeout for steam updates on profile cache
	 * @var integer
	 */
	public static $UPDATE_TIME = 3600; // 1 HOUR = 3600 seconds

	public static function getAPI()
	{
		return env('STEAM_API');
	}

	public static function canUpdate($smallId)
	{
		return !(Cache::has("profile_$smallId") && Cache::get("profile_$smallId") + self::$UPDATE_TIME > time());
	}

	public static function setUpdate($smallId)
	{
		Cache::put("profile_$smallId", time(), self::$UPDATE_TIME / 60);
	}

	public static function toSmallId($steam64BitId)
	{
		return self::converter($steam64BitId, function($steam64BitId) {
			return (int) explode('.', bcsub($steam64BitId, '76561197960265728'))[0];
		});
	}

	public static function to64bit($smallId)
	{
		return self::converter($smallId, function($smallId) {
			return ''.explode('.', bcadd($smallId,'76561197960265728'))[0];
		});
	}

	public static function to32Bit($steam64BitId)
	{
		return self::converter($steam64BitId, function($steam64BitId) {
			$steamIdPartOne = substr($steam64BitId, -1) % 2 == 0 ? 0 : 1;
			$steamIdPartTwo = bcsub($steam64BitId, '76561197960265728');

			if (bccomp($steamIdPartTwo,'0') != 1) return;

			$steamIdPartTwo = bcsub($steamIdPartTwo, $steamIdPartOne);
			$steamIdPartTwo = bcdiv($steamIdPartTwo, 2);

			return "STEAM_0:$steamIdPartOne:".explode('.', $steamIdPartTwo)[0];
		});
	}

	public static function toSteam3Id($steam64BitId)
	{
		return self::converter($steam64BitId, function($steam64BitId) {
			return 'U:1:'.self::toSmallId($steam64BitId);
		});
	}

	public static function aliasSort($a, $b) {
		return strcmp(self::aliasTimeConvert($b['timechanged']), self::aliasTimeConvert($a['timechanged']));
	}

	public static function aliasTimeConvert($time) {
		return strtotime(str_replace("@", "", $time));
	}

	public static function friendlyAlias($aliases)
	{
		if(is_null($aliases) || !is_array($aliases) || count($aliases) == 0) return [];

		$newAlias = [];

		foreach($aliases as $alias)
		{
			if(!isset($alias['newname']) || !isset($alias['timechanged'])) continue;

    		$newAlias[] = [
    			'newname' => $alias['newname'],
    			'timechanged' => date('M j Y', strtotime(str_replace("@", "", $alias['timechanged'])))
			];
		}

		return $newAlias;
	}

	public static function parseSearch($search) 
	{
		$statusChecker = array_filter(explode("\n", $search));
		$statusConfirm = false;
		$searchArray = array();

		foreach($statusChecker as $status)
		{
			if(substr(trim($status), 0, 1) == "#")
			{
				preg_match("/STEAM_.*?\s/", trim($status), $foundSteam);
				if(count($foundSteam) == 0) continue;

				$searchArray[] = trim($foundSteam[0]);
				$statusConfirm = true;
			}
		}

		if(!$statusConfirm) return array_filter(preg_split("/[,\s\n]+/", $search));

		return array_filter($searchArray);
	}

	private static function converter($values, $convert)
	{
		$isArray = is_array($values);
		$max = $isArray ? count($values) : 1;

		$converted = [0];

		for($i = 0; $i < $max; $i++)
		{
			$value = $isArray ? $values[$i] : $values;

			if(!is_numeric($value)) continue;

			$converted[$i] = $convert($value);
		}

		return $isArray ? $converted : $converted[0];
	}
}