<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
	protected $table = 'news';

	public function getPrev()
	{
		$prevNews = News::find($this->id - 1);

		if(!isset($prevNews->id)) return '';

		return '<a href="/news/'.$prevNews->id.'">&#8606; '.$prevNews->title.'</a>';
	}

	public function getNext()
	{
		$nextNews = News::find($this->id + 1);

		if(!isset($nextNews->id)) return '';

		return '<a href="/news/'.$nextNews->id.'">'.$nextNews->title.' &#8608;</a>';
	}
}
