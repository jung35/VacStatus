<?php

class mailList extends Eloquent {

  protected $table = 'mailList';

  public function steamUser() {
    return $this->belongsTo('steamUser');
  }
}
