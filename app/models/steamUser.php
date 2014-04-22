<?php

class steamUser extends Eloquent {

  protected $table = 'steamUser';

  public function vBanList() {
    return $this->hasMany('vBanList');
  }

  public function vBanUser() {
    return $this->hasMany('vBanUser','steam_id');
  }
}
