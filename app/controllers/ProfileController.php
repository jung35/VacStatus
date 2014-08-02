<?php

use Steam\Steam as Steam;

class ProfileController extends BaseController {

  public function profileAction($steam3Id = null)
  {
    if($steam3Id) {
      $profile = Profile::whereSteam3Id($steam3Id);
      if(!isset($profile->id)) {
        return View::make('profile/blankProfile', array('steam3Id' => $steam3Id));
      }
      return View::make('profile/profile');
    }

    return Redirect::home();
  }

  public function updateSingleProfileAction($steam3Id = null) {
    return View::make('profile/profileSkeleton');
  }

}
