<?php

use Steam\Steam as Steam;

class ProfileController extends BaseController {

  public function profileAction($steam3Id = null)
  {
    if($steam3Id) {
      $profile = Profile::whereSmallId(Steam::toSmallId($steam3Id))->first();

      if(!isset($profile->id)) {
        return View::make('profile/blankProfile')
        ->with('steam3Id', $steam3Id);
      }

      if(Steam::canUpdate(Steam::toSmallId($steam3Id))) {
        return View::make('profile/profile')
        ->with('steam3Id', $steam3Id)
        ->with('profile', $profile)
        ->with('update', true);
      }

      return View::make('profile/profile')
      ->with('steam3Id', $steam3Id)
      ->with('profile', $profile);
    }

    return Redirect::home();
  }

  public function updateSingleProfileAction($steam3Id = null) {

    if($steam3Id) {
      $profile = Profile::updateSingleProfile($steam3Id);

      if($profile == 'error') {
        // not stable connection to steam
        return App::abort(500);
      }

      return View::make('profile/profileSkeleton')
      ->with('profile', $profile);
    }
    return App::abort(500);
  }

}
