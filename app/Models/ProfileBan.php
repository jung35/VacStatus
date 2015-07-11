<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

use DateTime;
use DateInterval;

class ProfileBan extends Model
{
	protected $table = 'profile_ban';
	
	protected $dates = ['vac_banned_on'];

	 public $timestamps = true;

	public function Profile()
	{
		return $this->belongsTo('VacStatus\Models\Profile', 'profile_id', 'id');
	}

	public function isVacBanned()
	{
		return $this->vac > 0;
	}

	public function isCommunityBanned()
	{
		return $this->community;
	}

	public function isTradeBanned()
	{
		return $this->trade;
	}

	public function getVac()
	{
		return $this->vac;
	}

	public function getVacDays()
	{
		$date = new DateTime($this->vac_banned_on);
		return $this->isVacBanned() ? $date->format('M j Y') : 'None';
	}

	public function isUnbanned()
	{
		return $this->unban;
	}

	public function skipProfileBanUpdate($steamBan)
	{

		if($this->vac != (int) $steamBan->NumberOfVACBans + (int) $steamBan->NumberOfGameBans ||
			$this->community != $steamBan->CommunityBanned ||
			$this->trade != ($steamBan->EconomyBan != 'none'))
		{
			return false;
		}

		return true;
	}
}
