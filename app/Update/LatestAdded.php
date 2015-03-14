<?php namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;

use Cache;
use Carbon;

class LatestAdded extends BaseUpdate
{
	function __constructor()
	{
		$this->cacheName = "latestAdded";

		return $this->getLatestAdded();
	}

	private function getLatestAdded()
	{
		if(!$this->canUpdate()) return Cache::get($this->cacheName);

		// do update stuff thingy
	}
}