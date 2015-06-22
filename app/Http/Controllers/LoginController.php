<?php namespace VacStatus\Http\Controllers;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

use VacStatus\Models\User;

use VacStatus\Steam\Steam;
use VacStatus\Steam\SteamAPI;

use VacStatus\Update\SingleProfile;

use Cache;
use Auth;
use SteamAuth;

class LoginController extends Controller {
	
	public function login()
	{
		if(Auth::viaRemember())
		{
			return redirect()
				->intended('/list')
				->with('success','You have Successfully logged in.');
		}

		$steamuser = SteamAuth::Auth();
		$steam64BitId = str_replace("http://steamcommunity.com/openid/id/", "", $steamuser['steamid'] );

		// Try to grab user if it exists
		$user = User::whereSmallId((int) Steam::toSmallId($steam64BitId))->first();

		$steamAPI = new SteamAPI('info');
		$steamAPI->setSteamId($steam64BitId);

		$userSteamInfo = $steamAPI->run();

		if(isset($userSteamInfo->type) && $userSteamInfo->type == 'error' || !isset($userSteamInfo->response->players[0]))
		{
			return redirect()
				->intended('/')
				->with('error', 'There was an error trying to communicate with Steam Server.');
		}

		$userSteamInfo = $userSteamInfo->response->players[0];

		$steamAPI = new SteamAPI('friends');
		$steamAPI->setSteamId($steam64BitId);

		$userSteamFriends = $steamAPI->run();

		if(isset($userSteamInfo->type) && $userSteamInfo->type == 'error')
		{
			return redirect()
				->intended('/')
				->with('error', 'There was an error trying to communicate with Steam Server.');
		}

		$simpleFriends = [];

		if(isset($userSteamFriends->friendslist))
		{
			$userSteamFriends = $userSteamFriends->friendslist->friends;

			foreach($userSteamFriends as $userSteamFriend)
			{
				$simpleFriends[] = Steam::toSmallId($userSteamFriend->steamid);
			}
		}

		if(!isset($user->id))
		{
			$user = new User;
			$user->small_id = Steam::toSmallId($userSteamInfo->steamid);
		}

		$user->display_name = (string) $userSteamInfo->personaname;
		$user->friendslist = json_encode($simpleFriends);

		$smallId = Steam::toSmallId($steam64BitId);
		$singleProfile = new SingleProfile($smallId);
		$singleProfile->getProfile();

		if(!$user->save())
		{
			return redirect()
				->intended('/')
				->with('error', 'There was an error adding user to database');
		}

		Auth::login($user, true);

		return redirect()
			->intended('/list')
			->with('success','You have Successfully logged in.');
	}

    public function logout()
    {
		$this->middleware('auth');
        Auth::logout();

		return redirect()
			->route('home');
    }
}
