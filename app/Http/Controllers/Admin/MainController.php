<?php namespace VacStatus\Http\Controllers\Admin;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Cache;
use Input;

class MainController extends Controller {
	public function index()
	{
		return view('admin.pages.home');
	}

	public function announcementSave()
	{
		Cache::forever('announcement', Input::get('announcement'));

		return redirect()->route('admin.home');
	}
}