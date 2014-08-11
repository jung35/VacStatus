<?php

use Steam\Steam as Steam;
use Steam\SteamUser as SteamUser;

class HomeController extends BaseController {

  /**
   * Main page display
   * @return View index page
   */
  public function indexAction($uorl = 'most', $list = null)
  {
    Profile::updateMulitipleProfile(array(
      '76561198000575974',
      '76561198020317127'
    ));
    if($list == null) {
      $req = $uorl;
    } else {
      $req = array($uorl, $list);
    }
    $userList = UserList::getListType($req);
    return View::make('main/index', array('userList' => $userList));
  }

  public function searchSingleAction() {
    $search = Input::get('search');

    $steam3Id = SteamUser::findSteam3IdUser($search);

    if($steam3Id->type == 'error') {
      return Redirect::home();
    }

    return Redirect::route('profile', array('steam3Id' => $steam3Id->data));
  }
}
