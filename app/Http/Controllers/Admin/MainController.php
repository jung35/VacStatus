<?php namespace VacStatus\Http\Controllers\Admin;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

class MainController extends Controller {
	public function index()
	{
		return view('admin.pages.home');
	}
}