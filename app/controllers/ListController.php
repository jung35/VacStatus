<?php

use Steam\Steam as Steam;
use Steam\SteamUser as SteamUser;

class ListController extends BaseController {

    public function getAction()
    {
        $userId = Auth::User()->getId();
        $userList = UserList::whereRaw('user_id = ?', array($userId))->get();
        $simpleList = array();

        foreach($userList as $list) {
            $simpleList[] = array(
                'id' => $list->getId(),
                'user_id' => $list->user_id,
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

        if(UserList::whereUserId($userId)->count() >= Auth::User()->unlockList()) {
            return Response::make('Sorry, you have hit the maximum list creation.');
        }

        $userList = new UserList;
        $userList->user_id = $userId;
        $userList->title = $title;
        $userList->privacy = $privacy;

        if(!$userList->save()) {
            return Response::make('Sorry, there was an error trying to save.')
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

        if(!isset($userList->id)) {
            return Redirect::back()->with('error', 'Sorry, the list does not exist.');
        }

        $userList->title = $title;
        $userList->privacy = $privacy;

        if(!$userList->save()) {
            return Redirect::back()->with('error', 'Sorry, there was an error while trying to save.');
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

        if(!isset($userList->id)) {
            return Response::make('Invalid list or profile.');
        }

        $userListProfile = UserListProfile::whereRaw('user_list_id = ? and profile_id = ?', array($listId, $profileId))->first();

        if(isset($userListProfile->id)) {
            return Response::make('This user is already in the list.');
        }

        $count = UserListProfile::whereUserListId($listId)->count();

        if($count >= Auth::User()->unlockUser()) {
            return Response::make('Sorry, you have hit the maximum amount of users per list allowed.');
        }

        $userListProfile = new UserListProfile;
        $userListProfile->user_list_id = $listId;
        $userListProfile->profile_id = $profileId;
        $userListProfile->profile_description = $profileDescription;

        if(!$userListProfile->save()) {
            return Response::make('Sorry, there was an error while trying to save.');
        }

        return Response::make('success');
    }

    public function deleteUserAction()
    {
        $listId = Input::get('list_id');
        $profileId = Input::get('profile_id');
        $userId = Auth::User()->getId();

        $userList = UserList::whereRaw('id = ? and user_id = ?', array($listId, $userId))->first();

        if(!isset($userList->id)) {
            return Response::make('Invalid list or profile.');
        }

        $userListProfile = UserListProfile::whereRaw('user_list_id = ? and profile_id = ?', array($listId, $profileId))->first();

        if(!isset($userListProfile->id)) {
            return Response::make('This user is not on the list.');
        }

        if(!$userListProfile->delete()) {
            return Response::make('There was an error deleteing user.');
        }

        return Response::make('success');
    }

    public function addMultipleUserAction()
    {
        $listId = Input::get('list_id');
        $profileIds = Input::get('profile_ids');
        $profileDescription = Input::get('profile_description');
        $userId = Auth::User()->getId();

        $profileIds = array_filter(explode(",", $profileIds));

        $userList = UserList::whereRaw('id = ? and user_id = ?', array($listId, $userId))->first();

        if(!isset($userList->id)) {
            return Response::make('Invalid list or profile.');
        }

        $count = UserListProfile::whereUserListId($listId)->count();

        $userListProfile = UserListProfile::whereUserListId($listId)->whereIn('profile_id', $profileIds)->get();

        foreach($userListProfile as $userPresent) {
            unset($profileIds[array_search($userPresent->profile_id, $profileIds)]);
        }

        if(count($profileIds) == 0) {
            return Response::make('There was no user left to add on list');
        }

        $queryValues = array();
        $maxedOut = false;

        foreach($profileIds as $profileId) {
            if($count < Auth::User()->unlockUser()) {
                $count++;
                $queryValues[] = array(
                    'user_list_id' => $listId,
                    'profile_id' => $profileId,
                    'profile_description' => $profileDescription
                );
            } else {
                $maxedOut = true;
                break;
            }
        }

        if(!UserListProfile::insert($queryValues)) {
            return Response::make('There was an error trying to insert users into list.');
        }

        return Response::make($maxedOut ? 'maxed' : 'success');
    }
}
