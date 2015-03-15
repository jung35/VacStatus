<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
	protected $table = 'user_list';

	public function UserListProfile()
	{
		return $this->hasMany('VacStatus\Models\UserListProfile');
	}

	public function User()
	{
		return $this->belongsTo('VacStatus\Models\User', 'user_id', 'id');
	}

	public function canSubscribe($user_id)
	{
		return $this->user_id == $user_id || $this->privacy != 3;
	}
}