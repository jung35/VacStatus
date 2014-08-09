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
    } else {
      return Redirect::back()->with('error', 'Sorry, you have hit the maximum list creation.');
    }

    return Redirect::back()->with('success', 'List has been created.');
  }

  public function addUserAction() {
    $listId = Input::get('list_id');
    $profileId = Input::get('profile_id');
    $userId = Auth::User()->getId();

    $userList = UserList::whereRaw('id = ? and user_id = ?', array($listId, $userId))->first();
    if(isset($userList->id)) {
      $userListProfile = UserListProfile::whereRaw('user_list_id = ? and profile_id = ?', array($listId, $profileId))->first();
      if(!isset($userListProfile->id)) {
        $userListProfile = new UserListProfile;
        $userListProfile->user_list_id = $listId;
        $userListProfile->profile_id = $profileId;
        $userListProfile->save();
        return Redirect::back()->with('success', 'The user has been added to list.');
      }
      return Redirect::back()->with('error', 'This user is already in the list.');
    }
    return Redirect::back()->with('error', 'Invalid list or profile.');
  }
}
