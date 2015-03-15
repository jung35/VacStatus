<?php namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileBan extends Model
{
	protected $table = 'profile_ban';

	public function Profile()
	{
			return $this->belongsTo('Profile', 'profile_id', 'id');
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
		$currentBanDate = new DateTime($this->vac_banned_on);

		$newVacBanDate = new DateTime();
		$newVacBanDate->sub(new DateInterval("P{$steamBan->DaysSinceLastBan}D"));

		if($this->vac != $steamBan->NumberOfVACBans ||
			$this->community != $steamBan->CommunityBanned ||
			$this->trade != ($steamBan->EconomyBan != 'none') ||
			$currentBanDate->format("Y-m-d") != $newVacBanDate->format("Y-m-d"))
		{
			return false;
		}

		return true;
	}
}
