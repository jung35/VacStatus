<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserList extends Model
{
	use SoftDeletes;
	
	protected $table = 'user_list';

    protected $dates = ['deleted_at'];

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