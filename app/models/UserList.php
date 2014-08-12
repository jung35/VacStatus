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

  public static function getCount($userListProfiles = null) {
    $count = Array();

    if($userListProfiles == null) {
      $userListProfiles = UserListProfile::all();
    }

    foreach($userListProfiles as $userListProfile)
    {
      if(isset($count[$userListProfile->profile_id]))
      {
        $count[$userListProfile->profile_id]++;
      } else {
        $count[$userListProfile->profile_id] = 1;
      }
    }

    return $count;
  }

  public static function getListType($req) {
    $userList = null;

    if($req) {
      if(is_numeric($req)) { // requesting for self created list
        $userList = self::getMyList($req);
      } elseif(is_array($req)) { // requesting for friend's list (cannot be private)
        $userId = $req[0];
        $listId = $req[1];
        $userList = self::getUserList($userId, $listId);
      } else {
        switch($req) {
          case "most":
            $userList = self::getMostAdded();
            $userList->title = "Most Tracked";
            break;
          case "last":
            $userList = self::getLastAdded();
            $userList->title = "Latest Added";
            break;
        }
      }
    }

    return $userList;
  }

  public static function getMyList($listId) {
    if($listId && is_numeric($listId)) {
      $userList = UserList::whereRaw('id = ? and user_id = ?', Array($listId, Auth::user()->getId()))->first();

      if(isset($userList->id)) {
        $userListProfiles = UserListProfile::where('user_list_id', $listId)
          ->join('profile', 'user_list_profile.profile_id', '=', 'profile.id')
          ->join('profile_ban', 'user_list_profile.profile_id', '=', 'profile_ban.profile_id')
          ->orderBy('user_list_profile.id','desc')
          ->get();

        $count = self::getCount();

        foreach($userListProfiles as $key => $obj) {
          $userListProfiles[$key]->get_num_tracking = $count[$obj->profile_id];
        }

        $userListProfiles->title = $userList->getTitle();
        $userListProfiles->personal = true;

        return $userListProfiles;
      }
    }
    return null;
  }

  public static function getUserList($userId, $listId) {
    if($listId && is_numeric($listId)) {
      $userList = UserList::whereRaw('id = ? and user_id = ?', Array($listId, $userId))->first();

      if(isset($userList->id)) {
        if($userList->privacy == 1) {
          $userListProfiles = UserListProfile::where('user_list_id', $listId)
            ->join('profile', 'user_list_profile.profile_id', '=', 'profile.id')
            ->join('profile_ban', 'user_list_profile.profile_id', '=', 'profile_ban.profile_id')
            ->orderBy('user_list_profile.id','desc')
            ->get();

          $count = self::getCount();

          foreach($userListProfiles as $key => $obj) {
            $userListProfiles[$key]->get_num_tracking = $count[$obj->profile_id];
          }

          $userListProfiles->title = $userList->getTitle();
          $userListProfiles->personal = true;

          return $userListProfiles;
        }
      }
    }
    return null;
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
        $count[$userListProfile->profile_id]++;
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
    return (object) $userListProfiles;
  }

  public static function getLastAdded($limit = 20) {
    $userListProfiles = UserListProfile::join('profile', 'user_list_profile.profile_id', '=', 'profile.id')
      ->join('profile_ban', 'user_list_profile.profile_id', '=', 'profile_ban.profile_id')
      ->orderBy('user_list_profile.id','desc')
      ->get();

    $count = self::getCount($userListProfiles);

    $lastAddedProfiles = Array();

    // need to check if the latest has less than 20 people
    for($i = 0; $i < ($limit > $userListProfiles->count() ? $userListProfiles->count() : $limit) ; $i++) {
      $userListProfile = $userListProfiles[$i];
      $userListProfile->get_num_tracking = $count[$userListProfile->profile_id];
      $lastAddedProfiles[] = $userListProfile;
    }

    return (object) $lastAddedProfiles;
  }
}
