<?php namespace VacStatus\Http\Controllers;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

class MockUpController extends Controller {

	public function indexPage()
	{
		return view('pages/home');
	}

	public function mostTrackedPage()
	{
		return view('pages/list')->withGrab('most_tracked_users');
	}
}
