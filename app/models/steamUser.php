<?php

class steamUser extends Eloquent {

  protected $table = 'steamUser';

  public function vBanList() {
    return $this->hasMany('vBanList');
  }

  public function vBanUser() {
    return $this->hasOne('vBanUser','community_id','community_id');
  }
}
