<?php

use Steam\Steam as Steam;
use Steam\SteamUser as SteamUser;

class ListController extends BaseController {

  public function getAction() {
    $userId = Auth::User()->getId();
    $userList = UserList::whereRaw('user_id = ?', array($userId))->get();

    $simpleList = array();
    foreach($userList as $list) {
      $simpleList[] = array(
        'id' => $list->getId(),
        'title' => $list->getTitle(),
        'privacy' => $list->getPrivacy()
      );
    }

    return $simpleList;
  }

  public function createAction()
  {
    $title = Input::get('title') ?: 'My List';
    $privacy = Input::get('privacy') ?: 2;
    $userId = Auth::User()->getId();

    if(UserList::whereUserId($userId)->count() < Auth::User()->unlockList()) {
      $userList = new UserList;
      $userList->user_id = $userId;
      $userList->title = $title;
      $userList->privacy = $privacy;
      $userList->save();
    } else {
      return Response::make('Sorry, you have hit the maximum list creation.');
    }

    return Response::make('success');
  }

  public function editAction()
  {
    $listId = Input::get('list_id');
    $title = Input::get('title') ?: 'My List';
    $privacy = Input::get('privacy') ?: 1;
    $userId = Auth::User()->getId();

    $userList = UserList::whereRaw('id = ? and user_id = ?', array($listId, $userId))->first();

    if(isset($userList->id)) {
      $userList->title = $title;
      $userList->privacy = $privacy;
      $userList->save();
    } else {
      return Redirect::back()->with('error', 'Sorry, the list does not exist.');
    }

    return Redirect::back()->with('success', 'Changes on the list has saved.');
  }

  public function deleteAction()
  {
    $listId = Input::get('list_id');
    UserListProfile::whereUserListId($listId)->delete();
    UserList::find($listId)->delete();
    return Redirect::home()->with('success', 'List & its content has been deleted.');
  }

  public function addUserAction() {
    $listId = Input::get('list_id');
    $profileId = Input::get('profile_id');
    $profileDescription = Input::get('profile_description');
    $userId = Auth::User()->getId();

    $userList = UserList::whereRaw('id = ? and user_id = ?', array($listId, $userId))->first();
    if(isset($userList->id)) {
      $userListProfile = UserListProfile::whereRaw('user_list_id = ? and profile_id = ?', array($listId, $profileId))->first();
      if(!isset($userListProfile->id)) {
        $count = UserListProfile::whereUserListId($listId)->count();
        if($count < Auth::User()->unlockUser()) {
          $userListProfile = new UserListProfile;
          $userListProfile->user_list_id = $listId;
          $userListProfile->profile_id = $profileId;
          $userListProfile->profile_description = $profileDescription;
          $userListProfile->save();
          return Response::make('success');
        }
        return Response::make('Sorry, you have hit the maximum amount of users per list allowed.');
      }
      return Response::make('This user is already in the list.');
    }
    return Response::make('Invalid list or profile.');
  }

  public function deleteUserAction() {
    $listId = Input::get('list_id');
    $profileId = Input::get('profile_id');
    $userId = Auth::User()->getId();

    $userList = UserList::whereRaw('id = ? and user_id = ?', array($listId, $userId))->first();
    if(isset($userList->id)) {
      $userListProfile = UserListProfile::whereRaw('user_list_id = ? and profile_id = ?', array($listId, $profileId))->first();
      if(isset($userListProfile->id)) {
        $userListProfile->delete();
        return Response::make('success');
      }
      return Response::make('This user is not on the list.');
    }
    return Response::make('Invalid list or profile.');
  }
}
