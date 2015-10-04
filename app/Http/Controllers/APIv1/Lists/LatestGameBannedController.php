<?php
namespace VacStatus\Http\Controllers\APIv1\Lists;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Update\LatestGameBan;

class LatestGameBannedController extends Controller
{
	public function get()
	{
		$latestGameBan = new LatestGameBan;

		$return = [
			'list_info' => [ 'title' => 'Latest Game Banned Users' ],
			'profiles' => $latestGameBan->getList()
		];

		return $return;
	}
}