<?php namespace VacStatus\Http\Controllers;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

use VacStatus\Steam\Steam;

class PagesController extends Controller {

	public function indexPage()
	{
		return view('pages/home');
	}

	public function profilePage($steam64BitId)
	{
		return view('pages/profile', compact('steam64BitId'));
	}

	public function mostTrackedPage()
	{
		return view('pages/list')
			->withGrab('most');
	}

	public function latestTrackedPage()
	{
		return view('pages/list')
			->withGrab('latest');
	}
}
