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
use Socialite;

class LoginController extends Controller {

	public function sendToSteam()
	{
		if(Auth::check() || Auth::viaRemember())
		{
			return redirect()
				->intended('/list')
				->with('success','You have Successfully logged in!');
		}

		return Socialite::driver('steam')->redirect();
	}

	public function handleSteamLogin()
	{
        $user = Socialite::driver('steam')->user();

        dd($user);
	}
	
	public function login()
	{
		if(Auth::check() || Auth::viaRemember())
		{
			return redirect()
				->intended('/list')
				->with('success','You have Successfully logged in!');
		}

		$steamuser = SteamAuth::Auth();
		$steam64BitId = str_replace("http://steamcommunity.com/openid/id/", "", $steamuser['steamid'] );

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

		$simpleFriends = [];

		if(isset($userSteamFriends->friendslist))
		{
			$userSteamFriends = $userSteamFriends->friendslist->friends;

			foreach($userSteamFriends as $userSteamFriend)
			{
				$simpleFriends[] = Steam::toSmallId($userSteamFriend->steamid);
			}
		}

		$smallId = Steam::toSmallId($steam64BitId);

		// Try to grab user or create new one
		$user = User::firstOrCreate(['small_id' => $smallId]);

		$user->display_name = $userSteamInfo->personaname;
		$user->friendslist = json_encode($simpleFriends);

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
