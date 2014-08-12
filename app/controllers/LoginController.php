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
      return Redirect::home()->with('success','You have Successfully logged in.');
    }

    // Authenticate with Steam (using the details from our IoC Container).
    $hybridAuthProvider = $this->hybridAuth->authenticate( "Steam" );
    // Get user profile information
    $hybridAuthUserProfile = $hybridAuthProvider->getUserProfile();
    // Get Community ID
    $steam3Id = str_replace( "http://steamcommunity.com/openid/id/", "", $hybridAuthUserProfile->identifier );

    // Try to grab user if it exists
    $user = User::whereSmallId(Steam::toSmallId($steam3Id))->first();
    $userGrab = Steam::cURLSteamAPI('info', $steam3Id);

    if(isset($userGrab->type) && $userGrab->type == 'error') {
      return Redirect::home()->with('error', 'There was an error trying to communicate with Steam Server.');
    }

    $userGrab = $userGrab->response->players[0];

    if(!isset($user->id)) {

      $user = new User;
      $user->small_id = (string) Steam::toSmallId($userGrab->steamid);
    }

    $user->display_name = (string) $userGrab->personaname;
    $user->save();

    $steamAPI_friends = Steam::cURLSteamAPI('friends', $steam3Id);

    if(isset($userGrab->type) && $userGrab->type == 'error') {
      return Redirect::home()->with('error', 'There was an error trying to communicate with Steam Server.');
    }

    $simpleFriends = array();

    if(isset($steamAPI_friends->friendslist)) {
      $steamAPI_friends = $steamAPI_friends->friendslist->friends;

      foreach($steamAPI_friends as $steamAPI_friend) {
        $simpleFriends[] = Steam::toSmallId($steamAPI_friend->steamid);
      }
    }
    Session::put('friendsList', $simpleFriends);

    Auth::login($user, true);

    return Redirect::home()->with('success','You have Successfully logged in.');
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
