<?php

class UserList extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'user_list';

  public function User() {
    return $this->belongsTo('User', 'user_id', 'id');
  }

  public function getId() {
    return $this->id;
  }

  public function getTitle() {
    return $this->title;
  }
}
