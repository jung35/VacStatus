<?php

use Steam\Steam as Steam;

class HomeController extends BaseController {

  /*
  |--------------------------------------------------------------------------
  | Default Home Controller
  |--------------------------------------------------------------------------
  |
  | You may wish to use controllers instead of, or in addition to, Closure
  | based routes. That's great! Here is an example controller method to
  | get you started. To route to this controller, just add the route:
  |
  | Route::get('/', 'HomeController@showWelcome');
  |
  */

  public function showWelcome()
  {
    // var_dump(Steam::toBigId(Steam::toSmallId('76561198020317127')));
    // var_dump(Steam::cURLSteamAPI('info', array('76561198020317127'))->response->players);
    return View::make('main/index');
  }

}
