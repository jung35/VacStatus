<?php namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

use VacStatus\Update\MostTracked;
use VacStatus\Update\LatestTracked;

class ListController extends Controller {

	public function mostTracked()
	{
		$mostTracked = new MostTracked;

		$return = [
			'title' => 'Most Tracked Users',	
			'list' => $mostTracked->getMostTracked()
		];


		return $return;
	}

	public function latestTracked()
	{
		$latestTracked = new LatestTracked();

		$return = [
			'title' => 'Latest Tracked Users',
			'list' => $latestTracked->getLatestTracked()
		];

		return $return;
	}
}
