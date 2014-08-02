<?php

use Steam\Steam as Steam;

class ProfileController extends BaseController {

  public function profileAction($steam3Id = null)
  {
    if($steam3Id) {
      $profile = Profile::whereSteam3Id(Steam::toSmallId($steam3Id));
      if(!isset($profile->id)) {
        return View::make('profile/blankProfile', array('steam3Id' => $steam3Id));
      }

      if(Steam::canUpdate(strtotime($profile->updated_at))) {
        return View::make('profile/profile', Array('update', true));
      }

      return View::make('profile/profile');
    }

    return Redirect::home();
  }

  public function updateSingleProfileAction($steam3Id = null) {

    if($steam3Id) {
      $profile = new Profile;
      var_dump($profile->updateSingleProfile($steam3Id));
      return View::make('profile/profileSkeleton');
    }
    return "";
  }

}
