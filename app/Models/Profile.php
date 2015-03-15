<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
	protected $table = 'profile';

	public function ProfileOldAlias()
	{
		return $this->hasMany('VacStatus\Models\ProfileOldAlias');
	}

	public function ProfileBan()
	{
		return $this->hasOne('VacStatus\Models\ProfileBan');
	}

	public function isPrivate()
	{
		return $this->privacy != 3;
	}

	public function getSteamCreation()
	{
		if(isset($this->profile_created)) return date('M j Y', $this->profile_created);
		return "Unknown";
	}

	public function getAlias()
	{
		return json_decode($this->alias);
	}
}