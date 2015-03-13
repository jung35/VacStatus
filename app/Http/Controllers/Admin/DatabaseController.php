<?php namespace VacStatus\Http\Controllers\Admin;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\User;
use VacStatus\Models\Profile;

class DatabaseController extends Controller {
	public function user()
	{
		$users = User::paginate(50);
		return view('admin.pages.users', compact('users'));
	}

	public function profile()
	{
		$profiles = Profile::paginate(50);
		return view('admin.pages.profiles', compact('profiles'));
	}
}