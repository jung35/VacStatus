<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

use DateTime;
use DateInterval;
use Carbon;

class ProfileBan extends Model
{
	protected $table = 'profile_ban';
	
	protected $fillable = ['profile_id'];
	
	protected $dates = ['last_ban_date'];

	public $timestamps = false;

	public function Profile()
	{
		return $this->belongsTo('VacStatus\Models\Profile', 'profile_id', 'id');
	}

	public function isVacGameBanned()
	{
		return $this->vac_bans > 0 || $this->game_bans > 0;
	}

	public function isCommunityBanned()
	{
		return $this->community;
	}

	public function isTradeBanned()
	{
		return $this->trade;
	}

	public function getVacBans()
	{
		return $this->vac_bans;
	}

	public function getGameBans()
	{
		return $this->game_bans;
	}

	public function getVacDays()
	{
		$date = new DateTime($this->last_ban_date);
		return $this->isVacBanned() ? $date->format('M j Y') : 'None';
	}
}
