<?php

class ProfileOldAlias extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'profile_old_alias';

  public function Profile() {
      return $this->belongsTo('profile');
  }
}
