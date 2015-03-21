<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileOldAlias extends Model
{
	protected $table = 'profile_old_alias';
	
	protected $dates = ['seen'];

	public function Profile()
	{
		return $this->belongsTo('VacStatus\Models\Profile', 'profile_id', 'id');
	}

	public function compareTime($time)
	{
		return is_object($time) && $time->timestamp < $this->seen->timestamp ? $this->seen : $time;
	}
}
