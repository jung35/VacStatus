<?php

class Subscription extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'subscription';

  public function UserList() {
    return $this->hasOne('UserList', 'id', 'user_list_id');
  }

  public function canSubscribe() {
    return true;
  }
}
