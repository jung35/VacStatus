<?php

use Steam\Steam as Steam;

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

  public function getPrivacy() {
    return $this->privacy;
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
    if($listId && is_numeric($listId) && Auth::check()) {
      $userList = UserList::whereRaw('id = ? and user_id = ?', Array($listId, Auth::user()->getId()))->first();

      if(isset($userList->id)) {
        $userListProfiles = UserListProfile::where('user_list_id', $listId)
          ->join('profile', 'user_list_profile.profile_id', '=', 'profile.id')
          ->join('profile_ban', 'user_list_profile.profile_id', '=', 'profile_ban.profile_id')
          ->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
          ->orderBy('user_list_profile.id','desc')
          ->get([
            'user_list_profile.id',
            'user_list_profile.profile_id',
            'user_list_profile.user_list_id',

            'profile.small_id',
            'profile.display_name',
            'profile.privacy',
            'profile.avatar_thumb',
            'profile.avatar',
            'profile.profile_created',
            'profile.alias',
            'profile.created_at',
            'profile.updated_at',

            'profile_ban.community',
            'profile_ban.vac',
            'profile_ban.vac_days',
            'profile_ban.trade',
            'profile_ban.unban',

            'users.donation',
            'users.site_admin',
          ]);

        $count = self::getCount();

        $findUpdateFor = array();

        foreach($userListProfiles as $key => $obj) {
          if(Steam::canUpdate($obj->small_id)) {
            $findUpdateFor[] = $obj->small_id;
          }
          $userListProfiles[$key]->get_num_tracking = $count[$obj->profile_id];
        }

        $userListProfiles->title = $userList->getTitle();
        $userListProfiles->personal = true;
        $userListProfiles->custom = true;
        $userListProfiles->privacy = $userList->privacy;
        $userListProfiles->list_id = $listId;
        $userListProfiles->user_id = Auth::user()->getId();
        $userListProfiles->update = $findUpdateFor;

        return $userListProfiles;
      }
    }
    return null;
  }

  public static function getUserList($userId, $listId) {
    if($listId && is_numeric($listId)) {
      $userList = UserList::whereRaw('id = ? and user_id = ?', Array($listId, $userId))->first();

      if(isset($userList->id)) {
        if($userList->privacy != 3) {
          $stranger = User::whereId($userId)->first();
          if($userList->privacy == 2) {
            if(!Auth::check()) {
              return null;
            }
            if(!in_array($stranger->small_id, Session::get('friendsList'))) {
              return null;
            }
          }
          $userListProfiles = UserListProfile::where('user_list_id', $listId)
            ->join('profile', 'user_list_profile.profile_id', '=', 'profile.id')
            ->join('profile_ban', 'user_list_profile.profile_id', '=', 'profile_ban.profile_id')
            ->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
            ->orderBy('user_list_profile.id','desc')
            ->get([
              'user_list_profile.id',
              'user_list_profile.profile_id',
              'user_list_profile.user_list_id',

              'profile.small_id',
              'profile.display_name',
              'profile.privacy',
              'profile.avatar_thumb',
              'profile.avatar',
              'profile.profile_created',
              'profile.alias',
              'profile.created_at',
              'profile.updated_at',

              'profile_ban.community',
              'profile_ban.vac',
              'profile_ban.vac_days',
              'profile_ban.trade',
              'profile_ban.unban',

              'users.donation',
              'users.site_admin',
            ]);

          $count = self::getCount();

          $findUpdateFor = array();

          foreach($userListProfiles as $key => $obj) {
            if(Steam::canUpdate($obj->small_id)) {
              $findUpdateFor[] = $obj->small_id;
            }
            $userListProfiles[$key]->get_num_tracking = $count[$obj->profile_id];
          }

          $userListProfiles->title = $userList->getTitle();
          if(Auth::check() && $userId == Auth::user()->getId()) {
            $userListProfiles->personal = true;
          }
          $userListProfiles->privacy = $userList->privacy;
          $userListProfiles->custom = true;
          $userListProfiles->list_id = $listId;
          $userListProfiles->user_id = $userId;
          $userListProfiles->user_name = $stranger->getUserName();
          $userListProfiles->update = $findUpdateFor;

          return $userListProfiles;
        }
      }
    }
    return null;
  }

  public static function getMostAdded($limit = 20) {
    $userListProfiles = UserListProfile::join('profile', 'user_list_profile.profile_id', '=', 'profile.id')
      ->join('profile_ban', 'user_list_profile.profile_id', '=', 'profile_ban.profile_id')
      ->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
      ->get([
        'user_list_profile.id',
        'user_list_profile.profile_id',
        'user_list_profile.user_list_id',

        'profile.small_id',
        'profile.display_name',
        'profile.privacy',
        'profile.avatar_thumb',
        'profile.avatar',
        'profile.profile_created',
        'profile.alias',
        'profile.created_at',
        'profile.updated_at',

        'profile_ban.community',
        'profile_ban.vac',
        'profile_ban.vac_days',
        'profile_ban.trade',
        'profile_ban.unban',

        'users.donation',
        'users.site_admin',
      ]);

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
    $findUpdateFor = array();

    $userListProfiles = Array();
    $arrOfId = Array();
    if($arrCount > -1) {
      for($x = $arrCount; $x > $arrCount-($arrCount - $limit >= $limit ? $limit : $arrCount+1); $x--) {
        $keyOfId = array_search($newCount[$x], $count);
        $userListProfile = $profiles[$keyOfId];
        $userListProfile->get_num_tracking = $count[$keyOfId];

        if($userListProfile) {
          $userListProfiles[] = $userListProfile;
        }

        if(Steam::canUpdate($userListProfile->small_id)) {
          $findUpdateFor[] = $userListProfile->small_id;
        }

        unset($newCount[$x]);
        unset($count[$keyOfId]);
      }

      $userListProfiles = (object) $userListProfiles;
      $userListProfiles->update = $findUpdateFor;
    }
    return (object) $userListProfiles;
  }

  public static function getLastAdded($limit = 20) {
    $userListProfiles = UserListProfile::join('profile', 'user_list_profile.profile_id', '=', 'profile.id')
      ->join('profile_ban', 'user_list_profile.profile_id', '=', 'profile_ban.profile_id')
      ->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
      ->orderBy('user_list_profile.id','desc')
      ->get([
        'user_list_profile.id',
        'user_list_profile.profile_id',
        'user_list_profile.user_list_id',

        'profile.small_id',
        'profile.display_name',
        'profile.privacy',
        'profile.avatar_thumb',
        'profile.avatar',
        'profile.profile_created',
        'profile.alias',
        'profile.created_at',
        'profile.updated_at',

        'profile_ban.community',
        'profile_ban.vac',
        'profile_ban.vac_days',
        'profile_ban.trade',
        'profile_ban.unban',

        'users.donation',
        'users.site_admin',
      ]);


    $count = self::getCount($userListProfiles);

    $lastAddedProfiles = array();
    $findUpdateFor = array();

    // need to check if the latest has less than 20 people
    for($i = 0; $i < ($limit > $userListProfiles->count() ? $userListProfiles->count() : $limit) ; $i++) {
      $userListProfile = $userListProfiles[$i];
      $userListProfile->get_num_tracking = $count[$userListProfile->profile_id];
      $lastAddedProfiles[] = $userListProfile;
      if(Steam::canUpdate($userListProfile->small_id)) {
        $findUpdateFor[] = $userListProfile->small_id;
      }
    }
    $lastAddedProfiles = (object) $lastAddedProfiles;
    $lastAddedProfiles->update = $findUpdateFor;

    return $lastAddedProfiles;
  }
}
