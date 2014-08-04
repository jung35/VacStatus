<?php

use Steam\Steam as Steam;
use Steam\SteamUser as SteamUser;

class ListController extends BaseController {

  public function createAction()
  {
    $title = Input::get('title') ?: 'My List';
    $privacy = Input::get('privacy') ?: 2;

    $userList = new UserList;
    $userList->user_id = Auth::User()->getId();
    $userList->title = $title;
    $userList->privacy = $privacy;
    $userList->save();

    return Redirect::home();
  }
}
