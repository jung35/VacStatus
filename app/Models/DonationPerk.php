<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class DonationPerk extends Model
{
	protected $table = 'donation_perk';

	protected $hidden = ['created_at', 'updated_at'];

	public function getAmount()
	{
		return number_format($this->amount, 2, '.', '');
	}

	static public function getPerkAmount($perk)
	{
		$perk = self::wherePerk($perk)->first();

		return is_object($perk) ? $perk->amount:0;
	}
}
