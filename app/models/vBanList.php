<?php

class vBanList extends Eloquent {

  protected $table = 'vBanList';

  public function steamUser() {
    return $this->belongsTo('steamUser');
  }

  public function vBanUser() {
    return $this->hasOne('vBanUser','id','v_ban_user_id');
  }
}
