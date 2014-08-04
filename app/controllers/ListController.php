<?php

use Steam\Steam as Steam;
use Steam\SteamUser as SteamUser;

class ListController extends BaseController {

  public function createAction()
  {
    $title = Input::get('title') ?: 'My List';
    $privacy = Input::get('privacy') ?: 2;

    $userId = Auth::User()->getId();

    if(UserList::whereUserId($userId)->count() < Steam::$LIST_LIMIT) {
      $userList = new UserList;
      $userList->user_id = $userId;
      $userList->title = $title;
      $userList->privacy = $privacy;
      $userList->save();
    }

    return Redirect::home();
  }
}
