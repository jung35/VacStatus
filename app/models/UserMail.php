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

  public function Subscription() {
    return $this->hasMany('Subscription', 'user_id', 'user_id');
  }

  static public function checkUserList() {
    if(Cache::has('getLastCheckedUser')) {
      $getLastCheckedUser = Cache::get('getLastCheckedUser');
      $getNewUser = UserMail::whereRaw('id > ? and verify = ?', array($getLastCheckedUser, 'verified'))->first();

      if(!is_object($getNewUser)) {
        Cache::forget('getLastCheckedUser');
        Cache::forever('getLastCheckedUser', -1);
        return self::checkUserList();
      } else {
        Cache::forget('getLastCheckedUser');
        Cache::forever('getLastCheckedUser', $getNewUser->id);
      }
      return $getNewUser;
    }
    Cache::forever('getLastCheckedUser', -1);
    return self::checkUserList();
  }
}
