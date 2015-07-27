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
		if(Cache::has("profile_$smallId")
		   && Cache::get("profile_$smallId") + self::$UPDATE_TIME > time()) return false;

		return true;
	}

	public static function setUpdate($smallId)
	{
		Cache::put("profile_$smallId", time(), self::$UPDATE_TIME / 60);
	}

	public static function toSmallId($steam64BitId)
	{
		if(is_array($steam64BitId))
		{
			$smallIds = [];
			foreach($steam64BitId as $key => $value)
			{
				$smallIds[$key] = (int) explode('.', bcsub($value,'76561197960265728'))[0];
			}

			return $smallIds;
		}

		if(is_numeric($steam64BitId))
		{
			$steam64BitId .= '';
			return (int) explode('.', bcsub($steam64BitId,'76561197960265728'))[0];
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
				$steam64BitIds[$key] = ''.explode('.', bcadd($value,'76561197960265728'))[0];
			}

			return $steam64BitIds;
		}

		if(is_numeric($smallId))
		{
			$smallId .= '';
			return ''.explode('.', bcadd($smallId,'76561197960265728'))[0];
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
		if(is_numeric($steam64BitId)) return 'U:1:'.self::toSmallId($steam64BitId);

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
		if(is_null($aliases)) return [];

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

	public static function parseSearch($search) 
	{
		$statusChecker = array_filter(explode("\n", $search));
		$statusConfirm = false;
		$searchArray = array();

		foreach($statusChecker as $status)
		{
			if(substr(trim($status), 0, 1) == "#")
			{
				preg_match("(STEAM_.*?\s)", trim($status), $foundSteam);
				if(count($foundSteam) == 0) continue;

				$searchArray[] = $foundSteam[0];
				$statusConfirm = true;
			}
		}

		if(!$statusConfirm) return array_filter(preg_split("/[,\s\n]+/", $search));

		return array_filter($searchArray);
	}

	public static function findUser($data)
	{
		$data = strtolower(trim($data));

		if(empty($data)) return ['error' => 'Invalid or empty input'];

		if(strlen($data) > 100) return ['error' => 'Field too long'];
		
		if(substr($data, 0, 6) == 'steam_')
		{
			$tmp = explode(':', $data);

			if ((count($tmp) == 3)
			    && is_numeric($tmp[1])
			    && is_numeric($tmp[2]))
			{
				$steam3Id = bcadd(($tmp[2] * 2) + $tmp[1], '76561197960265728');
				return ['success' => $steam3Id];
			}
			return ['error' => 'Invalid Steam ID'];
		}
		else if (substr($data, 0, 2) == 'u:')
		{
			$tmp = explode(':', $data);

			if ((count($tmp) == 3) && is_numeric($tmp[2]))
			{
				$steam3Id = bcadd($tmp[2], '76561197960265728');
				return ['success' => $steam3Id];
			}

			return ['error' => 'Invalid Steam ID'];
		} else if ($p = strrpos($data, '/'))
		{
			$tmp = array_values(array_filter(explode('/',$data)));
			$a = null;

			foreach ($tmp as $key => $item)
			{
				if(!isset($tmp[$key+1]) || empty($tmp[$key+1])) return ['error' => 'Invalid input'];

				if ($item == 'profiles')
				{
					$a = $tmp[$key+1];
					break;
				} else if ($item == 'id')
				{
					$data = $tmp[$key+1];
					break;
				}
			}

			if (is_numeric($a) && preg_match('/7656119/', $a)) return ['success' => $a];
			else {
				$steamAPI = new SteamAPI('vanityUrl');
				$steamVanityUrl = $steamAPI->setSteamId($data)->run();

				if(isset($steamVanityUrl->type) && $steamVanityUrl->type == 'error'
					|| !isset($steamVanityUrl->response->steamid)
						&& $steamVanityUrl->response->success == 42) return ['error' => 'Invalid input'];

				$steamid64 = (string) $steamVanityUrl->response->steamid;

				if (!preg_match('/7656119/', $steamid64)) return ['error' => 'Invalid link'];
				else return ['success' => $steamid64];
			}
		}
		else if (is_numeric($data) && preg_match('/7656119/', $data))
		{
			return ['success' => $data];
		}
		else
		{
			$steamAPI = new SteamAPI('vanityUrl');
			$steamVanityUrl = $steamAPI->setSteamId($data)->run();

			if(isset($steamVanityUrl->type) && $steamVanityUrl->type == 'error' ||
			   !isset($steamVanityUrl->response) ||
			   !isset($steamVanityUrl->response->steamid) && $steamVanityUrl->response->success == 42)
			{
				return ['error' => 'Invalid input'];
			}

			$steamid64 = (string) $steamVanityUrl->response->steamid;

			if (!preg_match('/7656119/', $steamid64)) return ['error' => 'Invalid input'];
			else return ['success' => $steamid64];
		}
	}
}