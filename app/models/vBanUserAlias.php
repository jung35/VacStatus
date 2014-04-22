<?php

class vBanUserAlias extends Eloquent {

  protected $table = 'vBanUserAlias';

  public function vBanUser() {
      return $this->belongsTo('vBanUser');
  }
}
