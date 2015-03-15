<?php namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

use VacStatus\Update\MostTracked;

class SpecialTrackController extends Controller
{

	public function mostTracked()
	{
		$mostTracked = new MostTracked();
		$mostTracked = $mostTracked->getMostTracked();

		return $mostTracked;
	}

}
