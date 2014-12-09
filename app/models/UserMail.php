<?php

class UserMail extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'user_mail';

  public function canMail()
  {
    return $this->verify == 'verified';
  }
}
