<?php

use Steam\Steam as Steam;

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

  public function updateSingleProfile($steam3Id = null) {

    if($steam3Id) {
      Steam::cURLSteamAPI('info', )
      $profile = Profile::whereSteam3Id($steam3Id);

      if(isset())
      return 'hi';
    }
  }
}
