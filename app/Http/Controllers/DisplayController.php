<?php

namespace VacStatus\Http\Controllers;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

use VacStatus\Steam\Steam;

use VacStatus\Models\News;

use Input;
use Cache;
use Carbon;

class DisplayController extends Controller {

	public function searchPage()
	{
		$searchQuery = Input::get('search');

		if(!$searchQuery) return redirect()->intended('/');

		$randomString = str_random(12);
		$searchCacheName = "search_key_";

		if(Cache::has($searchCacheName.$randomString))
			while(Cache::has($searchCacheName.$randomString))
				$randomString = str_random(12);

		$expiresAt = Carbon::now()->addMinutes(10);

		Cache::put($searchCacheName.$randomString, $searchQuery, $expiresAt);

		return redirect("/search/$randomString");
	}
}
