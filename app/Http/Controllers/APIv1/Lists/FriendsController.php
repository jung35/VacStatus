<?php

namespace VacStatus\Http\Controllers\APIv1\Lists;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Update\Friends;

class FriendsController extends Controller
{
	public function get()
	{
		$friends = new Friends;

		$return = [
			'list_info' => [ 'title' => 'Friends' ],
			'profiles' => $friends->getList()
		];

		return $return;
	}
}