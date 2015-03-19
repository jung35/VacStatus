<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserListProfile extends Model
{
	use SoftDeletes;
	
	protected $table = 'user_list_profile';

    protected $dates = ['deleted_at'];

	public function UserList()
	{
		return $this->belongsTo('VacStatus\Models\UserList', 'user_list_id', 'id');
	}

	public function Profile()
	{
		return $this->hasOne('VacStatus\Models\Profile', 'id');
	}
}
