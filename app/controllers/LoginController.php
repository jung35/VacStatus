<?php

class LoginController extends \BaseController {

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

    // Authenticate with Steam (using the details from our IoC Container).
    $hybridAuthProvider = $this->hybridAuth->authenticate( "Steam" );
    // Get user profile information
    $hybridAuthUserProfile = $hybridAuthProvider->getUserProfile();
    // Get Community ID
    $steam3Id = str_replace( "http://steamcommunity.com/openid/id/", "", $hybridAuthUserProfile->identifier );

    // Try to grab user if it exists
    $user = User::wheresmallId(Steam\Steam::toSmallId($steam3Id))->first();

    if(!isset($user->id)) {

      $userGrab = Steam\Steam::cURLSteamAPI('info', $steam3Id);

      if($userGrab->type == 'error') {
        return Redirect::home();
      }

      $userGrab = $userGrab->response->players[0];

      $user = new User;
      $user->small_id = (string) $userGrab->steamid;
      $user->display_name = (string) $userGrab->personaname;
      $user->save();

      $this->log->addInfo("newAccount", array(
        "UserId" => $user->id,
        "displayName" => $user->display_name,
        "ipAddress" => Request::getClientIp()
      ));
    }
    Session::regenerate();

    Session::put('user.name', $user->display_name);
    Session::put('user.communityId', $steam3Id);
    Session::put('user.id', $user->id);
    Session::put('user.in', true);

    if(isset($user->site_admin) && $user->site_admin > 0) {
      Session::put('user.admin', $user->site_admin);
    }

    $this->log->addInfo("Login", array(
      "UserId" => $user->id,
      "displayName" => $user->display_name,
      "ipAddress" => Request::getClientIp()
    ));

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
    //
  }

}
