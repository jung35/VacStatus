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
    	$socUser = Socialite::driver('steam')->user();

    	if(is_null($socUser->getId()))
    	{
			return redirect()
				->intended('/')
				->with('error', 'There was an error trying to communicate with Steam Server.');
    	}

        $steamAPI = new SteamAPI($socUser->getId());
		$userSteamFriends = $steamAPI->fetch('friends');
		
		$simpleFriends = [];

		if(isset($userSteamFriends['friendslist']))
		{
			$simpleFriends = $this->getFriends($userSteamFriends['friendslist']['friends']);
		}

		$smallId = Steam::toSmallId($socUser->getId());

		// Try to grab user or create new one
		$user = User::firstOrNew(['small_id' => $smallId]);

		$user->display_name = $socUser->getNickname();
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
			->route('home')
			->with('success','You have Successfully logged out.');
    }

    private function getFriends(array $friends)
    {
    	$return = [];

		foreach($friends as $friend)
		{
			$return[] = Steam::toSmallId($friend['steamid']);
		}

		return $return;
    }
}
