<?php

class DisplayListController extends \BaseController {

  public function fetchListAction() {
    $req = Input::get('req');
    $title = null;
    $userList = null;

    if($req) {
      if(is_numeric($req)) { // requesting for self created list
        $userList = UserList::getMyList($req);
        $title = $userList->title;
      } elseif(is_array($req)) { // requesting for friend's list (cannot be private)

      } else {
        switch($req) {
          case "most":
            $title = "Most Tracked";
            $userList = UserList::getMostAdded();
            break;
          case "last":
            $title = "Latest Added";
            $userList = UserList::getLastAdded();
            break;
        }
      }
    }

    if($userList == null) {
      return App::abort(500);
    }
    return View::make('list/listTable', array('title' => $title, 'userList' => $userList));
  }
}
