<?php namespace VacStatus\Http\Controllers\Admin;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\News;

use Input;

class NewsController extends Controller {
	public function index()
	{
        $news = News::orderBy('id', 'desc')
        	->paginate(20);

        return view('admin/pages/news/index', compact('news'));
	}

	public function editForm(News $news)
	{
        return view('admin/pages/news/edit', compact('news'));
	}

	public function saveNews($newsId = null)
	{
        $newsTitle = Input::get('news_title');
        $newsBody = Input::get('news_body');

		if(is_null($newsId))
		{
        	$news = new News;
		} else {
			$news = News::where('id', $newsId)->first();
			if(!isset($news->id)) $news = new News;
		}

        $news->title = $newsTitle;
        $news->body = $newsBody;
        $news->save();

        return redirect()->route('admin.news');
	}

	public function delete(News $news)
	{
		$news->delete();
        return redirect()->route('admin.news');
	}
}