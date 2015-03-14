<?php namespace VacStatus\Update;

use Cache;
use Carbon;

class LatestAdded
{
	protected $cacheName = "latestAdded";
	protected $cacheLength = 3600; // seconds

	function canUpdate()
	{
		if(Cache::has($this->cacheName))
		{
			return false;
		}

		return true;
	}

	function updateCache($mostTracked)
	{
		if(Cache::has($this->cacheName)) Cache::forget($this->cacheName);

		Cache::put($this->cacheName, $mostTracked, Carbon::now()->addSeconds($this->cacheLength));
	}

	function getLatestAdded()
	{
		if(!$this->canUpdate()) return Cache::get($this->cacheName);

		// do update stuff thingy
	}
}