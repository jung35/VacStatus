<?php

namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\News;
use VacStatus\Models\Announcement;

use VacStatus\Steam\Steam;

use Auth;

class MainController extends Controller
{
	public function index ()
	{
		$news = News::orderBy('id', 'desc')->take(2)->get();

		$parsedNews = [];

		foreach($news as $article)
		{
			$parsedNews[] = [
				'id' => $article->id,
				'title' => $article->title,
				'created_at' => $article->created_at->format("M j Y"),
			];
		}

		$announcement = Announcement::latest();

		return compact('parsedNews', 'announcement');
	}

	public function navbar ()
	{
		$user = new \stdClass;

		if(Auth::check())
		{
			$user = Auth::user();
			$user->profile;
			$user->steam_64_id = Steam::to64bit($user->small_id);
		}

		return compact('user');
	}
}