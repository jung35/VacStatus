<?php

namespace VacStatus\Update;

use Cache;
use Carbon;

class BaseUpdate
{
	protected $cacheName;
	protected $cacheLength = 60; // in minutes

	protected function hasCache()
	{
		return Cache::has($this->cacheName);
	}

	protected function updateCache($data)
	{
		if($this->hasCache()) Cache::forget($this->cacheName);

		$expireTime = Carbon::now()->addMinutes($this->cacheLength);

		Cache::put($this->cacheName, $data, $expireTime);
	}

	protected function grabCache()
	{
		return $this->hasCache() ? Cache::get($this->cacheName) : false;
	}

	protected function error($reason)
	{
		return ['error' => $reason ];
	}
}