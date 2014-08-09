<?php

class DisplayListController extends \BaseController {

  public function fetchListAction() {
    $req = Input::get('req');
    $title = null;
    $userList = null;

    if($req) {
      if(is_numeric($req)) { // requesting for self created list

      } elseif(is_array($req)) { // requesting for friend's list (cannot be private)

      } else {
        switch($req) {
          case "most":
            $userList = UserList::getMostAdded();
            break;
          case "last":
            $userList = UserList::getLastAdded();
            break;
        }
      }
    }

    if($userList == null) {
      return App::abort(500);
    }
    return View::make('list/listTable', array('userList' => $userList));
  }
}
