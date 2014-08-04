<?php

use Steam\Steam as Steam;

class LoginController extends \BaseController {

  private $hybridAuth;

  function __construct(Hybrid_Auth $hybridAuth) {
    // parent::__construct();

    $this->hybridAuth = $hybridAuth;
  }

  /**
   * Auth user using hybridauth to steam
   * GET /login
   *
   * @return redirect to home
   */
  public function loginAction($action = '')
  {

    if ( $action == "auth" ) {
      try {
         Hybrid_Endpoint::process();
      }
      catch ( Exception $e ) {
         echo "Error at Hybrid_Endpoint process (LoginController@index): $e";
      }
      return;
    }

    if(Auth::viaRemember()) {
      return Redirect::home();
    }

    // Authenticate with Steam (using the details from our IoC Container).
    $hybridAuthProvider = $this->hybridAuth->authenticate( "Steam" );
    // Get user profile information
    $hybridAuthUserProfile = $hybridAuthProvider->getUserProfile();
    // Get Community ID
    $steam3Id = str_replace( "http://steamcommunity.com/openid/id/", "", $hybridAuthUserProfile->identifier );

    // Try to grab user if it exists
    $user = User::whereSmallId(Steam::toSmallId($steam3Id))->first();

    if(!isset($user->id)) {

      $userGrab = Steam::cURLSteamAPI('info', $steam3Id);

      if(isset($userGrab->type) && $userGrab->type == 'error') {
        return Redirect::home();
      }

      $userGrab = $userGrab->response->players[0];

      $user = new User;
      $user->small_id = (string) Steam::toSmallId($userGrab->steamid);
      $user->display_name = (string) $userGrab->personaname;
      $user->save();
    }

    Auth::login($user, true);

    return Redirect::home();
    //
  }

  /**
   * removes auth to guest
   * GET /logout
   *
   * @return redirect to home
   */
  public function logoutAction()
  {
    Hybrid_Auth::logoutAllProviders();
    Auth::logout();

    return Redirect::home();

  }

}
