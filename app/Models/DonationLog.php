<?php

namespace VacStatus\Models;

use Illuminate\Database\Eloquent\Model;

class DonationLog extends Model
{
	protected $table = 'donation_log';

	public function isValid()
	{
		return $this->status == 'Completed';
	}
}
