<?php namespace VacStatus\Update;

use VacStatus\Update\BaseUpdate;

use Cache;
use Carbon;

class MostTracked extends BaseUpdate
{
	function __constructor()
	{
		$this->cacheName = "mostTracked";
		
		return $this->getMostTracked();
	}

	private function getMostTracked()
	{
		if(!$this->canUpdate()) return Cache::get($this->cacheName);

		// do update stuff thingy
	}
}