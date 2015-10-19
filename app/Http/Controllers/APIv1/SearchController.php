<?php

namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Steam\Steam;
use VacStatus\Steam\SteamUser;

use VacStatus\Update\MultiProfile;

use VacStatus\Models\Profile;
use VacStatus\Models\UserListProfile;

use Cache;
use Auth;
use DateTime;

class SearchController extends Controller
{
	public function search($searchKey)
	{
		$searchCache = "search_key_$searchKey";
		if(!Cache::has($searchCache)) return ['error' => 'no values'];

		$search = Steam::parseSearch(Cache::get($searchCache));
		if(!is_array($search)) return ['error' => 'Invalid Search Option'];

		if(Auth::check())
		{
			if(count($search) > Auth::User()->unlockSearch())
			{
				return ['error' => 'Too many profiles listed in search box.'];
			}
		}
		elseif(count($search) > 30)
		{
			return ['error' => 'Too many profiles listed in search box for a guest.'];
		}

		$validProfile = (new SteamUser($search))->fetch();

		if(!is_array($validProfile) || count($validProfile) == 0)
		{
			return ['error' => 'None of the profiles were found to be valid steam accounts'];
		}

		$smallIds = Steam::toSmallId($validProfile);

		$profiles = Profile::whereIn('profile.small_id', $smallIds)->getProfileData();

		$multiProfile = new MultiProfile($profiles);
		$profiles = $multiProfile->run();

		$return = [
			'list_info' => [ 'title' => 'Search Result' ],
			'profiles' => $profiles
		];

		return $return;
	}
}