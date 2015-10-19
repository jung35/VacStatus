<?php

namespace VacStatus\Steam;

class SteamUser {

	private $data;
	private $isArray;

	function __construct($data)
	{
		if(is_array($data))
		{
			$this->data = $this->cleanArray($data);
			$this->isArray = true;
		} else {
			$this->data = strtolower(trim($data));
			$this->isArray = false;
		}
	}

	public function fetch() // returns steam 64bit id
	{
		$checkData = $this->checkData();
		if($checkData) return $checkData;

		$data = $this->data;
		$isArray = $this->isArray;

		if(!$isArray) $data = [$data];

		$validIds = [];

		foreach($data as $value)
		{
			if($this->verify32Bit($value)) $validIds[] = $this->convert32Bit($value);
			elseif($this->verifyUID($value)) $validIds[] = $this->convertUID($value);
			elseif($this->verifyId($value)) $validIds[] = $value;
			else $validIds[] = $this->convertVanityURL($value);
		}

		$validIds = $this->cleanArray($validIds);

		if(!isset($validIds[0])) return 'Invalid Input';
		
		if($isArray) return $validIds;
		return $validIds[0];
	}

	private function cleanArray($array)
	{
		$array = array_map('trim', $array);
		$array = array_map('strtolower', $array);
		$array = array_filter($array, function($value) {
			$len = strlen($value);
			return $len <= 100 && $len > 0;
		});
		$array = array_values($array);

		return $array;
	}

	private function checkData()
	{
		$isArray = $this->isArray;
		$data = $this->data;

		if(($isArray && count($data) == 0)
			|| (!$isArray && (strlen($data) < 1 || strlen($data) > 100)))
		{
			return 'Invalid Input';
		}

		return false;
	}

	private function verifyId($value)
	{
		return is_numeric($value) && preg_match('/7656119/', $value);
	}

	private function verify32Bit($value)
	{
		return substr($value, 0, 6) == 'steam_';
	}

	private function verifyUID($value)
	{
		return substr($value, 0, 2) == 'u:';
	}

	private function convert32Bit($value)
	{
		$tmp = explode(':', $value);

		if (count($tmp) == 3 && is_numeric($tmp[1]) && is_numeric($tmp[2])) 
		{
			return bcadd(($tmp[2] * 2) + $tmp[1], '76561197960265728');
		}

		return;
	}

	private function convertUID($value)
	{
		$tmp = explode(':', $value);

		if (count($tmp) == 3 && is_numeric($tmp[2]))
		{
			return bcadd($tmp[2], '76561197960265728');
		}

		return;
	}

	private function convertVanityURL($value)
	{
		$tmp = explode('/', $value);
		$tmp = array_filter($tmp);
		$tmp = array_values($tmp);

		foreach ($tmp as $key => $item)
		{
			if(!isset($tmp[$key + 1]) || empty($tmp[$key + 1])) break;
			elseif ($item == 'profiles')
			{
				$value = $tmp[$key + 1];
				return $this->verifyId($value) ? $value : null;
			}
			elseif ($item == 'id')
			{
				$tmp = $tmp[$key + 1];
				break;
			}
		}

		$steamAPI = new SteamAPI($tmp);
		$steamVanityUrl = $steamAPI->fetch('vanityUrl');

		if(!isset($steamVanityUrl['response']['steamid'])) return;

		$steamid64 = (string) $steamVanityUrl['response']['steamid'];

		if(!$this->verifyId($steamid64)) return;

		return $steamid64;
	}
}