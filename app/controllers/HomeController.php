<?php

use Steam\Steam as Steam;
use Steam\SteamUser as SteamUser;

class HomeController extends BaseController {

  /**
   * Main page display
   * @return View index page
   */
  public function indexAction($uorl = 'most', $list = null)
  {
    /*
      Updates multiple profile with the most efficiently way possible (I hope)

      Use strings because with int, it rounds early and string seems like easy fix.
     */
    if($list == null) {
      $req = $uorl;
    } else {
      $req = array($uorl, $list);
    }

    $userList = UserList::getListType($req);

    $friendsList = array();

    if(Auth::check()) {
      $userId = Auth::User()->getId();
      if(Cache::has('friendsList_'.$userId) && count(Cache::get('friendsList_'.$userId)) != 0) {
        $friendsList = User::whereIn('small_id', Cache::get('friendsList_'.$userId))->get();
      }
    }

    return View::make('main/index', array('userList' => $userList, 'friendsList' => $friendsList));
  }

  public function searchSingleAction() {
    $search = Input::get('search');

    $steam3Id = SteamUser::findSteam3IdUser($search);

    if($steam3Id->type == 'error') {
      return Redirect::home()->with('error', 'Could not find a user based on given information.');
    }

    return Redirect::route('profile', array('steam3Id' => $steam3Id->data));
  }

  public function searchMultipleAction() {
    /*
      1 - Default
      2 - 'status' from console paste
     */
    $searchType = Input::get('search_type');

    $search = Input::get('search');

    if(!isset($searchType) || !isset($search)) {
      return Redirect::home()->with('error', 'Invalid fields.');
    }

    $search = array_filter(explode("\n", $search));

    if(count($search) > Auth::User()->unlockSearch()) {
      return Redirect::home()->with('error', 'Too many profiles listed in search box.');
    }

    switch($searchType) {
      case 1:
        if(is_array($search)) {
          $validProfile = Array();
          $invalidProfile = Array();

          foreach($search as $potentialProfile) {
            $steam3Id = SteamUser::findSteam3IdUser($potentialProfile);

            if($steam3Id->type == 'error') {
              $invalidProfile[] = $potentialProfile;
            } else {
              $validProfile[] = $steam3Id->data;
            }
          }

          $userList = Profile::updateMulitipleProfile($validProfile);
          if(!is_object($userList)) {
            return Redirect::home()->with('error', 'None of the users exist');
          }
          $userList->title = "Search";

          return View::make('main/search', array('userList' => $userList, 'invalidProfile' => implode(", ", $invalidProfile)));
        }
        break;
      case 2:
        break;
    }

    return Redirect::home()->with('error', 'Invalid Search Option');
  }

  private function searchMutlipleDefault($search) {
  }
}
