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
    // var_dump(Steam::cURLSteamAPI('info', array('76561198020317127'))->response->players);
    return View::make('main/index');
  }

}
