<?php namespace VacStatus\Http\Controllers;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

use VacStatus\Steam\Steam;

use VacStatus\Models\News;

class PagesController extends Controller {

	public function indexPage()
	{
		$news = News::orderBy('id', 'desc')->take(2)->get();

		$parsedNews = [];

		foreach($news as $article)
		{
			$parsedNews[] = [
				'id' => $article->id,
				'title' => $article->title,
				'created_at' => $article->created_at->format("M j Y"),
			];
		}

		return view('pages/home', compact('parsedNews'));
	}

	public function profilePage($steam64BitId)
	{
		return view('pages/profile', compact('steam64BitId'));
	}

	public function listListPage()
	{
		return view('pages/listPortal');
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

	public function customListPage($listId)
	{
		return view('pages/list')
			->withGrab($listId);
	}

	public function newsPage($page = 1)
	{
		return view('pages/news')
			->withPage($page);
	}

	public function privacyPage()
	{
		return view('pages/privacy');
	}

	public function contactPage()
	{
		return view('pages/contact');
	}

	public function donatePage()
	{
		return view('pages/donate');
	}
}
