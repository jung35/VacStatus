<?php namespace VacStatus\Update;

class BaseUpdate
{
	protected $cacheName;
	protected $cacheLength = 3600; //seconds

	private function canUpdate()
	{
		if(Cache::has($this->cacheName))
		{
			return false;
		}

		return true;
	}

	private function updateCache($data)
	{
		if(Cache::has($this->cacheName)) Cache::forget($this->cacheName);

		Cache::put($this->cacheName, $data, Carbon::now()->addSeconds($this->cacheLength));
	}
}