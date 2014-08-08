<?php

class UserListProfile extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'user_list_profile';

  public function UserList() {
    return $this->belongsTo('UserList', 'user_list_id', 'id');
  }
}
