<?php

class vBanUser extends Eloquent {

  protected $table = 'vBanUser';

  public function vBanList() {
    return $this->belongsTo('vBanList');
  }

  public function steamUser() {
    return $this->belongsTo('steamUser');
  }

  public function vBanUserAlias() {
    return $this->hasMany('vBanUserAlias');
  }
}
