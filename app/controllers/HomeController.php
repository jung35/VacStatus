<?php

use Steam\Steam as Steam;

class HomeController extends BaseController {

  /**
   * Main page display
   * @return View index page
   */
  public function showWelcome()
  {
    // var_dump(Steam::toBigId(Steam::toSmallId('76561198020317127')));
    var_dump(Steam::cURLSteamAPI('ban', array('76561198020317127','76561198047414609')));
    return View::make('main/index');
  }

}
