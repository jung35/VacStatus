<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

use Michelf\Markdown;
use Carbon;

class News extends Model
{
	protected $table = 'news';

	public function getBodyAttribute($body)
	{
		return Markdown::defaultTransform($body);
	}

	public function getCreatedAtAttribute($createdAt)
	{
		return (new Carbon($createdAt))->format("M j Y");
	}

	public function scopeLatest($query, $amount = 2)
	{
		return $query->orderBy('id', 'desc')->take($amount)->get();
	}
}
