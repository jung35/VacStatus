<?php

use Steam\Steam as Steam;

class ProfileController extends BaseController {

  public function profileAction($steam3Id = null)
  {
    if($steam3Id) {
      $profile = Profile::where('profile.small_id',Steam::toSmallId($steam3Id))
        ->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
        ->first([
          'profile.id',
          'profile.small_id',
          'profile.display_name',
          'profile.privacy',
          'profile.avatar_thumb',
          'profile.avatar',
          'profile.profile_created',
          'profile.alias',
          'profile.created_at',
          'profile.updated_at',
          'users.donation',
          'users.site_admin',
        ]);

      /*
        Check if all db exists (just incase when it page was canceled when trying to load)
       */
      if(!isset($profile->id) ||
         !is_object($profile->ProfileBan) ||
         !is_object($profile->ProfileOldAlias)) {
        return View::make('profile/blankProfile')
        ->with('steam3Id', $steam3Id);
      }


      $old = Array(1, 0);
      if(Cache::has("checked_$steam3Id")) {
        $old = Array(Cache::get("checked_$steam3Id"), Cache::get("checked_time_$steam3Id"));
      }
      $gettingCount = UserListProfile::whereProfileId($profile->id)
        ->orderBy('id','desc')
        ->get();

      $profile->getCount = UserList::getCount($gettingCount);
      $profile->getCount = isset($profile->getCount[$profile->id])? $profile->getCount[$profile->id] : 0;
      $profile->lastCount = isset($gettingCount[0]) ? strtotime($gettingCount[0]->created_at) : 0;

      if(Steam::canUpdate(Steam::toSmallId($steam3Id))) {
        return View::make('profile/profile')
        ->with('steam3Id', $steam3Id)
        ->with('profile', $profile)
        ->with('old_check', $old)
        ->with('update', true);
      }

      if(Cache::has("checked_$steam3Id")) {
        Cache::forever("checked_$steam3Id", $old[0] + 1);
      } else {
        Cache::forever("checked_$steam3Id", 1);
      }

      Cache::forever("checked_time_$steam3Id", time());

      return View::make('profile/profile')
      ->with('steam3Id', $steam3Id)
      ->with('profile', $profile)
      ->with('old_check', $old);
    }

    return Redirect::home();
  }

  public function updateSingleProfileAction() {

    $steam3Id = Input::get('steam3Id');

    if($steam3Id) {
      $profile = Profile::updateSingleProfile($steam3Id);

      if($profile == 'error') {
        // not stable connection to steam
        return App::abort(500);
      }

      $old = Array(1, 0);
      if(Cache::has("checked_$steam3Id")) {
        $old = Array(Cache::get("checked_$steam3Id"), Cache::get("checked_time_$steam3Id"));
        Cache::forever("checked_$steam3Id", $old[0] + 1);
      } else {
        Cache::forever("checked_$steam3Id", 1);
      }

      return View::make('profile/profileSkeleton')
      ->with('profile', $profile)
      ->with('old_check', $old);
    }
    return App::abort(500);
  }

  public function updateMultipleProfileAction() {
    $steam3Ids = Input::get('steam3Ids');

    if($steam3Ids && is_array($steam3Ids)) {
      $profiles = Profile::updateMulitipleProfile($steam3Ids);
      if($profiles == 'error') {
        // not stable connection to steam
        return App::abort(500);
      }
    }
    return App::abort(500);
  }

}
