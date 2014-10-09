<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Steam\Steam as Steam;

class User extends Eloquent implements UserInterface, RemindableInterface {

  use UserTrait, RemindableTrait;

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'users';

  public function isAdmin() {
    return $this->site_admin == 1;
  }

  /**
   * Connect to the old aliases that this user was seen with
   */
  public function UserList() {
    return $this->hasMany('UserList');
  }

  public function getId() {
    return $this->id;
  }

  public function getUserName() {
    return $this->display_name;
  }

  public function getSmallId() {
    return $this->small_id;
  }

  public function getSteam3Id() {
    return Steam::toBigId($this->small_id);
  }

  public function getDonation() {
    return $this->donation;
  }

}
