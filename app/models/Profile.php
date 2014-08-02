<?php

class Profile extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'profile';

  public function ProfileOldAlias() {
    return $this->hasMany('profile_old_alias');
  }
}
