<?php
namespace VacStatus\Http\Controllers\APIv1\Lists;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Update\MostTracked;

class MostTrackedController extends Controller
{
	public function get()
	{
		$mostTracked = new MostTracked;

		$return = [
			'list_info' => [ 'title' => 'Most Tracked Users' ],
			'profiles' => $mostTracked->getList()
		];

		return $return;
	}
}