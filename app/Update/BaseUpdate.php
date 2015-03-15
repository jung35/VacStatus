<?php namespace VacStatus\Update;

use Cache;
use Carbon;

class BaseUpdate
{
	protected $cacheName;
	protected $cacheLength = 1; //60; // in minutes

	protected function canUpdate()
	{
		if(Cache::has($this->cacheName))
		{
			return false;
		}

		return true;
	}

	protected function updateCache($data)
	{
		if(Cache::has($this->cacheName)) Cache::forget($this->cacheName);

		$expireTime = Carbon::now()->addMinutes($this->cacheLength);

		Cache::put($this->cacheName, $data, $expireTime);
	}
}