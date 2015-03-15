<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
	protected $table = 'subscription';

	public function UserList()
	{
		return $this->hasOne('VacStatus\Models\UserList', 'id', 'user_list_id');
	}
}
