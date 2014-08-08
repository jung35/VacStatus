<?php

class DisplayListController extends \BaseController {

  public function mostListedAction()
  {
    $UserListProfiles = UserListProfile::join('profile', 'user_list_profile.profile_id', '=', 'profile.id')->get();

    $count = Array();
    $profiles = Array();

    foreach($UserListProfiles as $UserListProfile)
    {
      if(isset($count[$UserListProfile->profile_id]))
      {
        $count[$UserListProfile->profile_id] += 1;
      } else {
        $count[$UserListProfile->profile_id] = 1;
        $profiles[$UserListProfile->profile_id] = $UserListProfile;
      }
    }

    $newCount = $count;
    sort($newCount);

    $arrCount = count($newCount)-1;

    $UserListProfiles = Array();
    $arrOfId = Array();
    if($arrCount > -1) {
      for($x = $arrCount; $x > $arrCount-($arrCount - 20 >= 20 ? 20 : $arrCount+1); $x--)
      {
        $keyOfId = array_search($newCount[$x], $count);
        $UserListProfile = $profiles[$keyOfId];
        $UserListProfile->get_num_tracking = $count[$keyOfId];

        if($UserListProfile) {
          $UserListProfiles[] = $UserListProfile;
        }

        unset($newCount[$x]);
        unset($count[$keyOfId]);
      }
    }
    dd($UserListProfiles);
  }
}
