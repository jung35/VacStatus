<?php
namespace VacStatus\Http\Controllers\APIv1\Lists;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Update\LatestVAC;

class LatestVACBannedController extends Controller
{
	public function get()
	{
		$latestVac = new LatestVAC;

		$return = [
			'list_info' => [ 'title' => 'Latest VAC Banned Users' ],
			'profiles' => $latestVac->getLatestVAC()
		];

		return $return;
	}
}