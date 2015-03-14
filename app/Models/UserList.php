<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
	protected $table = 'user_list';

	public function UserListProfile()
	{
		return $this->hasMany('UserListProfile');
	}

	public function User()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}

	public function canSubscribe($user_id)
	{
		return $this->user_id == $user_id || $this->privacy != 3;
	}
}