<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileOldAlias extends Model
{
	protected $table = 'profile_old_alias';

	public function Profile()
	{
		return $this->belongsTo('Profile', 'profile_id', 'id');
	}

	public function addAlias(Profile $profile)
	{
		$this->profile_id = $profile->id;
		$this->seen = time();
		$this->seen_alias = $profile->display_name;

		return $this->save();
	}

	public function compareTime($time)
	{
		return $time < $this->seen ? $this->seen : $time;
	}
}
