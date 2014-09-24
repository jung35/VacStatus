<?php

class ProfileBan extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'profile_ban';

  public function Profile() {
      return $this->belongsTo('Profile', 'profile_id', 'id');
  }

  public function isVacBanned() {
    return $this->vac > 0;
  }

  public function isCommunityBanned() {
    return $this->community;
  }

  public function isTradeBanned() {
    return $this->trade;
  }

  public function getVac() {
    return $this->vac;
  }

  public function getVacDays() {
    return $this->isVacBanned() ? date('M j Y', time() - ($this->vac_days * 24 * 60 * 60)) : 'None';
  }

  public function isUnbanned() {
    return $this->unban;
  }
}
