<?php

class UserList extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'user_list';

  public function UserListProfile() {
    return $this->hasMany('UserListProfile');
  }

  public function User() {
    return $this->belongsTo('User', 'user_id', 'id');
  }

  public function getId() {
    return $this->id;
  }

  public function getTitle() {
    return $this->title;
  }

  public static function getMostAdded($limit = 20) {
    $userListProfiles = UserListProfile::join('profile', 'user_list_profile.profile_id', '=', 'profile.id')
      ->join('profile_ban', 'user_list_profile.profile_id', '=', 'profile_ban.profile_id')
      ->get();

    $count = Array();
    $profiles = Array();

    foreach($userListProfiles as $userListProfile)
    {
      if(isset($count[$userListProfile->profile_id]))
      {
        $count[$userListProfile->profile_id] += 1;
      } else {
        $count[$userListProfile->profile_id] = 1;
        $profiles[$userListProfile->profile_id] = $userListProfile;
      }
    }

    $newCount = $count;
    sort($newCount);

    $arrCount = count($newCount)-1;

    $userListProfiles = Array();
    $arrOfId = Array();
    if($arrCount > -1) {
      for($x = $arrCount; $x > $arrCount-($arrCount - $limit >= $limit ? $limit : $arrCount+1); $x--)
      {
        $keyOfId = array_search($newCount[$x], $count);
        $userListProfile = $profiles[$keyOfId];
        $userListProfile->get_num_tracking = $count[$keyOfId];

        if($userListProfile) {
          $userListProfiles[] = $userListProfile;
        }

        unset($newCount[$x]);
        unset($count[$keyOfId]);
      }
    }
    return $userListProfiles;
  }
}
