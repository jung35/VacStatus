<?php namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\News;

use \Michelf\Markdown;

class NewsController extends Controller
{
	public function index()
	{//Markdown::defaultTransform

		$news = News::orderBy('id', 'desc')->paginate(10);

		$parsedNews = [
			'next_page' => $news->nextPageUrl(),
			'prev_page' => $news->previousPageUrl(),
			'current_page' => $news->currentPage(),
			'data' => []
		];

		foreach($news as $article) {
			$parsedNews['data'][] = [
				'id' => $article->id,
				'title' => $article->title,
				'body' => Markdown::defaultTransform($article->body),
				'created_at' => $article->created_at->diffForHumans(),
			];
		}

		return $parsedNews;
	}

	public function showArticle(News $news)
	{
		$parsedNews = [
			'id' => $news->id,
			'title' => $news->title,
			'body' => Markdown::defaultTransform($news->body),
			'created_at' => $news->created_at->diffForHumans(),
		];

		return $parsedNews;
	}

}