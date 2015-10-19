<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

use Michelf\Markdown;

class Announcement extends Model
{
	protected $fillable = ['value'];

    public function scopeLatest($query)
    {
    	return Markdown::defaultTransform($query->orderBy('created_at', 'desc')->first()['value']);
    }
}
