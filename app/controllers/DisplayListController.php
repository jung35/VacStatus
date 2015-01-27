<?php

use Steam\Steam as Steam;

class DisplayListController extends \BaseController {

    public function fetchListAction() {
        $req = Input::get('req');
        $userList = null;

        if($req) {
            $userList = UserList::getListType($req);
        }

        if($userList == null) {
            return 'error';
        }

        if(Auth::check() && isset($userList->custom)) {
            return View::make('list/listTable', array(
                'userList'     => $userList,
                'userMail'     => Auth::User()->UserMail,
                'subscription' => Subscription::whereUserListId($userList->list_id)->first()
            ));
        }

        return View::make('list/listTable', array('userList' => $userList));
    }

    public function updateListAction() {

        $list = Input::get('list');

        if(!is_array($list)) return '';

        $userList = Profile::updateMulitipleProfile(Steam::toBigId($list));

        if(!is_object($userList)) return '';

        return View::make('list/listRowDisplay', array('userList' => $userList));

    }
}
