<?php

class ProfileOldAlias extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'profile_old_alias';

  public function Profile() {
      return $this->belongsTo('Profile', 'profile_id', 'id');
  }

  public function addAlias(Profile $profile) {
    $this->profile_id = $profile->getId();
    $this->seen = time();
    $this->seen_alias = $profile->getDisplayName();

    $this->save();
    return;
  }

  public function compareTime($time) {
    return $time < $this->seen ? $this->seen : $time;
  }

  public function getAlias() {
    return $this->seen_alias;
  }

  public function getTime() {
    return $this->seen;
  }
}
