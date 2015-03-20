<?php namespace VacStatus\Http\Controllers;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

class SettingsController extends Controller
{
	public function subscriptionPage()
	{
		$this->middleware('auth');
		return view('settings/subscription');
	}
}