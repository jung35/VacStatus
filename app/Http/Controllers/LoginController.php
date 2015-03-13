<?php namespace VacStatus\Http\Controllers;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

use VacStatus\Steam\Steam;

class LoginController extends Controller {
	private $hybridAuth;

	function __construct(Hybrid_Auth $hybridAuth)
	{
		parent::__construct();
		$this->hybridAuth = $hybridAuth;
	}
	
	public function loginAction($action = '')
	{
		if ( $action == "auth" ) {
			try {
				Hybrid_Endpoint::process();
			} catch ( Exception $e ) {
				echo "Error at Hybrid_Endpoint process (LoginController@index): $e";
			}
			return;
		}

		if(Auth::viaRemember()) {
			return Redirect::home()->with('success','You have Successfully logged in.');
		}

		// Authenticate with Steam (using the details from our IoC Container).
		$hybridAuthProvider = $this->hybridAuth->authenticate( "Steam" );
		$hybridAuthUserProfile = $hybridAuthProvider->getUserProfile();
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
		if(!$user->save()) {
			return Redirect::home()->with('error', 'There was an error adding user to database');
		}
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
		Auth::login($user, true);
		Cache::forever('friendsList_'. Auth::User()->getId(), $simpleFriends);
		return Redirect::home()->with('success','You have Successfully logged in.');
	}

    public function logoutAction()
    {
        Hybrid_Auth::logoutAllProviders();
        Auth::logout();
        return Redirect::home();
    }
}