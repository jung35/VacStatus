<?php

class HomeController extends BaseController {

  private $authedUser;

  public function __construct(Hybrid_Auth $hybridAuth)
  {
    parent::__construct();
    $this->hybridAuth = $hybridAuth;
  }

  public function showWelcome()
  {
    return View::make('hello');
  }

  public function steamLogin($action = '')
  {
    if ( $action == "auth" ) {
      try {
         Hybrid_Endpoint::process();
      }
      catch ( Exception $e ) {
         echo "Error at Hybrid_Endpoint process (HomeController@steamLogin): $e";
      }
      return;
    }

    // Authenticate with Steam (using the details from our IoC Container).
    $hybridAuthProvider = $this->hybridAuth->authenticate( "Steam" );
    // Get user profile information
    $hybridAuthUserProfile = $hybridAuthProvider->getUserProfile();
    // Get Community ID
    $steamCommunityId = str_replace( "http://steamcommunity.com/openid/id/", "", $hybridAuthUserProfile->identifier );

    $steamUser = steamUser::wherecommunityId($steamCommunityId)->first();

    if(!isset($steamUser->id)) {
      $steamUserGrab = $this->cURLPage("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->steamAPI}&steamids={$steamCommunityId}&".time()) or
        die($this->log->addError("fileLoad", array(
          "steamUserId" => Session::get('user.id'),
          "displayName" => Session::get('user.name'),
          "ipAddress" => Request::getClientIp(),
          "controller" => "steamLogin@HomeController"
        )));

      if(!$steamUserGrab) {
        return Redirect::to('/');
      }

      $steamUserGrab = $steamUserGrab->response->players[0];

      $steamUser = new steamUser;
      $steamUser->community_id = (string) $steamUserGrab->steamid;
      $steamUser->display_name = (string) $steamUserGrab->personaname;
      $steamUser->save();

      $this->log->addInfo("newAccount", array(
        "steamUserId" => $steamUser->id,
        "displayName" => $steamUser->display_name,
        "ipAddress" => Request::getClientIp()
      ));
    }

    $userInfo = $this->getVBanUser($steamCommunityId, $steamUser->id);
    Session::put('user.name', $steamUser->display_name);
    Session::put('user.communityId', $steamCommunityId);
    Session::put('user.id', $steamUser->id);
    Session::put('user.in', true);

    if(isset($steamUser->site_admin) && $steamUser->site_admin > 0) {
      Session::put('user.admin', $steamUser->site_admin);
    }

    $this->log->addInfo("Login", array(
      "steamUserId" => $steamUser->id,
      "displayName" => $steamUser->display_name,
      "ipAddress" => Request::getClientIp()
    ));

    return Redirect::to('/');
  }

  public function steamLogout()
  {
    $steamUserId = Session::get("user.id");
    $steamUserDisplayName = Session::get("user.name");
    Session::forget('user.name');
    Session::forget('user.communityId');
    Session::forget('user.id');
    Session::forget('user.in');
    Session::forget('user.admin');
    Session::forget('email.send');
    $this->hybridAuth->logoutAllProviders();

    $this->log->addInfo("Logout", array(
      "steamId" => $steamUserId,
      "displayName" => $steamUserDisplayName,
      "ipAddress" => Request::getClientIp()
    ));

    return Redirect::to('/');
  }

  public function showNews() {
    $siteNews = DB::table('siteNews')->orderBy('id','desc')->paginate(20);
    return View::make('siteNews', array('siteNewses' => $siteNews));
  }

  public function showAbout() {
    return View::make('aboutContacts');
  }

}
