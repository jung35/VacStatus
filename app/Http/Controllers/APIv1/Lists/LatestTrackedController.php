<?php
namespace VacStatus\Http\Controllers\APIv1\Lists;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Update\LatestTracked;

class LatestTrackedController extends Controller
{
	public function get()
	{
		$latestTracked = new LatestTracked;

		$return = [
			'list_info' => [ 'title' => 'Latest Tracked Users' ],
			'profiles' => $latestTracked->getLatestTracked()
		];

		return $return;
	}
}