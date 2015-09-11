<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public function scopeLatest($query)
    {
    	return $query->orderBy('created_at', 'desc')->first();
    }
}
