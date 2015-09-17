<?php namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\News;

class NewsController extends Controller
{
	public function index()
	{
		$news = News::orderBy('id', 'desc')->paginate(10);

		return $news;
	}
}