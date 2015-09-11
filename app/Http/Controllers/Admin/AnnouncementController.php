<?php

namespace VacStatus\Http\Controllers\Admin;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\Announcement;

class AnnouncementController extends Controller {
	public function index()
	{
		$announcement = Announcement::orderBy('created_at', 'desc')->paginate(50);

		return view('admin.pages.announcement.main', compact('announcement'));
	}
}