<?php

use Steam\Steam as Steam;
use Steam\SteamUser as SteamUser;

class HomeController extends BaseController {

  /**
   * Main page display
   * @return View index page
   */
  public function indexAction()
  {
    $mostAdded = UserList::getMostAdded();
    // var_dump(Steam::toBigId(Steam::toSmallId('76561198020317127')));
    // var_dump(Steam::cURLSteamAPI('ban', array('76561198020317127','76561198047414609')));
    return View::make('main/index', array('mostAdded' => $mostAdded));
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
