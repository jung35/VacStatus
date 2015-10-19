<?php

namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\News;
use VacStatus\Models\Announcement;

use VacStatus\Steam\Steam;

use Auth;

class MainController extends Controller
{
	public function index()
	{
		$news = News::latest();
		$announcement = Announcement::latest();

		return compact('news', 'announcement');
	}

	public function navbar()
	{
		$user = (object) [];

		if(Auth::check())
		{
			$user = Auth::user();
			$user->profile;
			$user->steam_64_id = Steam::to64bit($user->small_id);
		}

		return compact('user');
	}
}