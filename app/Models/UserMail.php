<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

use Cache;

class UserMail extends Model
{
	protected $table = 'user_mail';

	public function Subscription()
	{
		return $this->hasMany('VacStatus\Models\Subscription', 'user_id', 'user_id');
	}

	public function canMail()
	{
		return $this->verify == 'verified';
	}

	public function canPushbullet()
	{
		return $this->pushbullet_verify == 'verified';
	}
}
