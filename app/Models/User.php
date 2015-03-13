<?php namespace VacStatus\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use VacStatus\Steam\Steam;

class User extends Model implements AuthenticatableContract { 
	use Authenticatable;

	protected $table = 'users';

	public function isAdmin() {
		return $this->site_admin == 1;
	}
	/**
	 * Connect to the old aliases that this user was seen with
	 */
	public function UserList() {
		return $this->hasMany('UserList');
	}

	public function Subscription() {
		return $this->hasMany('Subscription');
	}

	public function UserMail() {
		return $this->hasOne('UserMail');
	}

	public function getSteam3Id() {
		return Steam::toBigId($this->small_id);
	}

	public function getDonation() {
		return number_format($this->donation, 2, '.', '');
	}

	public function unlockList() {
		if($this->isAdmin()) {
			return 999;
		}
		if($this->donation >= DonationPerk::getPerkAmount('list_1')) {
			return 20;
		}
		if($this->beta == 1) {
			return 3;
		}
		return 1;
	}

	public function unlockUser() {
		if($this->isAdmin()) {
			return 999;
		}
		if($this->donation >= DonationPerk::getPerkAmount('user_1')) {
			return 75;
		}
		if($this->beta == 1) {
			return 35;
		}
		return 30;
	}

	public function unlockSearch() {
		if($this->isAdmin()) {
			return 999;
		}
		if($this->donation >= DonationPerk::getPerkAmount('search_1')) {
			return 70;
		}
		if($this->beta == 1) {
			return 50;
		}
		return 30;
	}

	public function unlockSubscription() {
		if($this->isAdmin()) {
			return 999;
		}
		if($this->donation >= DonationPerk::getPerkAmount('subscription')) {
			return 25;
		}
		if($this->beta == 1) {
			return 5;
		}
		return 3;
	}
	
	public function addDonation($amount) {
		if(is_numeric($this->donation)) {
			$this->donation += $amount;
		} else {
			$this->donation = $amount;
		}
	}
}