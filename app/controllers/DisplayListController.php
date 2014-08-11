<?php

class DisplayListController extends \BaseController {

  public function fetchListAction() {
    $req = Input::get('req');
    $title = null;
    $userList = null;

    if($req) {
      $userList = UserList::getListType($req);
    }

    if($userList == null) {
      return App::abort(500);
    }
    return View::make('list/listTable', array('userList' => $userList));
  }
}
