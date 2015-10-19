<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
	use SoftDeletes;

	protected $table = 'subscription';
	
	protected $dates = ['deleted_at'];

	public function UserList()
	{
		return $this->hasOne('VacStatus\Models\UserList', 'user_list_id', 'id');
	}
}
