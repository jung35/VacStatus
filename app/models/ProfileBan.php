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

  public function getNote() {
    if($this->isVacBanned()) {
      if($this->vac_days < 8) {
        return "This user's ban is still new. It may just be a cooldown. ";
      } else if($this->vac_days < 90) {
        return "There is nothing to say about this user except the fact that this person got rekt. Volvo OP";
      } else if($this->vac_days > 365) {
        return "There is nothing to say about this user except the fact that this person has an old ban.";
      } else {
        return "There is nothing to say about this user except the fact that this person has a ban.";
      }
    } else {
      if($this->isUnbanned()) {
        return "This user was previously banned. This could mean that this person had a temperary ban and/or was unbanned. ";
      } else {
        return "There is nothing to say about this user. ";
      }
    }
  }

  public function isUnbanned() {
    return $this->unban;
  }
}
