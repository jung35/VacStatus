<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class UserListProfile extends Model
{
	protected $table = 'user_list_profile';

	public function UserList()
	{
		return $this->belongsTo('UserList', 'user_list_id', 'id');
	}

	public function Profile()
	{
		return $this->hasOne('Profile', 'id');
	}
}
