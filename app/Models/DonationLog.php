<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

use VacStatus\Steam\Steam;

class DonationLog extends Model
{
	protected $table = 'donation_log';
    protected $casts = [
    	// users.*
        'site_admin' => 'integer', 
        'donation' => 'integer',
        'beta' => 'integer',
    ];

	public function isValid()
	{
		return $this->status == 'Completed';
	}

	public function scopeLatest($query, $amount = 10)
	{
		return $query->whereStatus('Completed')
			->leftjoin('users', 'donation_log.small_id', '=', 'users.small_id')
			->orderBy('donation_log.id', 'desc')
			->take($amount)
			->get([
				'donation_log.original_amount',

				'users.display_name',
				'users.small_id',

				'users.donation',
				'users.beta',
				'users.site_admin',
			]);
	}

	public function toArray()
	{
		$array = parent::toArray();
		$array['steam_64_bit'] = $this->steam_64_bit;

		return $array;
	}

	public function getSteam64BitAttribute()
	{
		return Steam::to64Bit($this->small_id);
	}

	// public function getSiteAdminAttribute($value)
	// {
	// 	return (int) $value?:0;
	// }

	// public function getDonationAttribute($value)
	// {
	// 	return (int) $value?:0;
	// }

	// public function getBetaAttribute($value)
	// {
	// 	return (int) $value?:0;
	// }
}
