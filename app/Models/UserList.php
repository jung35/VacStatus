<?php

use Steam\Steam as Steam;

class UserList extends \Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_list';

	public function UserListProfile() {
		return $this->hasMany('UserListProfile');
	}

	public function User() {
		return $this->belongsTo('User', 'user_id', 'id');
	}

	public function canSubscribe($user_id) {
		return $this->user_id == $user_id || $this->privacy != 3;
	}
}