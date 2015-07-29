<?php namespace VacStatus\Http\Controllers\Admin;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;
use VacStatus\LogFetch;

use Cache;
use Input;

class MainController extends Controller {
	public function index()
	{
		$logFetch = new LogFetch;
		return view('admin.pages.home', compact('logFetch'));
	}

	public function announcementSave()
	{
		Cache::forever('announcement', Input::get('announcement'));

		return redirect()->route('admin.home');
	}

	public function viewLog($filename)
	{
		$logFetch = new LogFetch;
		$logData = $logFetch->viewLog($filename);

		if(!$logData) return redirect()->route('admin.home');

		return view('admin.pages.log', compact('logData'));
	}
}