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
      return App::abort(500);
    }
    return View::make('list/listTable', array('userList' => $userList));
  }

  public function updateListAction() {

    $list = Input::get('list');

    if(!is_array($list)) {
      return '';
    }

    $userList = Profile::updateMulitipleProfile(Steam::toBigId($list));

    if(!is_object($userList)) {
      dd($userList, 'wtf');
    }

    return View::make('list/listRowDisplay', array('userList' => $userList));

  }
}
