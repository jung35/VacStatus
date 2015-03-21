<?php namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use VacStatus\Steam\Steam;

use VacStatus\Update\MultiProfile;

use VacStatus\Models\Profile;

use Cache;
use Auth;
use DateTime;

class SearchController extends Controller
{
	public function search($searchKey)
	{
		$searchCache = "search_key_$searchKey";

		if(!Cache::has($searchCache)) return ['error' => 'no values'];

		$search = Cache::pull($searchCache);
		Cache::forget($searchCache);
		
		$statusChecker = array_filter(explode("\n", $search));
		$statusConfirm = false;
		$searchArray = array();

		foreach($statusChecker as $status)
		{
			if(substr(trim($status), 0, 1) == "#")
			{
				preg_match("(STEAM_.*?\s)", trim($status), $foundSteam);
				if(count($foundSteam) == 0) continue;
				$searchArray[] = $foundSteam[0];
				$statusConfirm = true;
			}
		}

		if(!$statusConfirm)
		{
			$search = array_filter(preg_split("/[\s\n]+/", $search));
		} else {
			$search = array_filter($searchArray);
		}

		if(Auth::check())
		{
			if(count($search) > Auth::User()->unlockSearch())
			{
				return ['error' => 'Too many profiles listed in search box.'];
			}
		} else if(count($search) > 30)
		{
			return ['error' => 'Too many profiles listed in search box.'];
		}

		if(!is_array($search))
		{
			return ['error' => 'Invalid Search Option'];
		}

		$validProfile = Array();
		$invalidProfile = Array();
		foreach($search as $potentialProfile)
		{
			$steam3Id = Steam::findUser($potentialProfile);

			if(isset($steam3Id['error'])) {
				$invalidProfile[] = $potentialProfile;
			} else {
				$validProfile[] = $steam3Id['success'];
			}
		}

		$smallIds = Steam::toSmallId($validProfile);

		$profiles = Profile::select(
			'profile.id',
			'profile.display_name',
			'profile.avatar_thumb',
			'profile.small_id',
			'profile.created_at',

			'profile_ban.vac',
			'profile_ban.vac_banned_on',
			'profile_ban.community',
			'profile_ban.trade',

			'users.site_admin',
			'users.donation',
			'users.beta',

			\DB::raw('max(user_list_profile.created_at) as created_at'),
			\DB::raw('count(user_list_profile.id) as total')
			)->groupBy('profile.id')
			->leftJoin('user_list_profile', function($join)
			{
				$join->on('user_list_profile.profile_id', '=', 'profile.id')
					->whereNull('user_list_profile.deleted_at');
			})->whereIn('profile.small_id', $smallIds)
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->get();

		$profilesParsed = [];

		foreach($smallIds as $smallId)
		{
			$profile = $profiles->where('small_id', (int) $smallId)->first();

			if(is_null($profile))
			{
				$profilesParsed[] = [
					'small_id' => $smallId
				];
				continue;
			}

			$vacBanDate = new DateTime($profile->vac_banned_on);

			$profilesParsed[] = [
				'id'			=> $profile->id,
				'display_name'	=> $profile->display_name,
				'avatar_thumb'	=> $profile->avatar_thumb,
				'small_id'		=> (int) $smallId,
				'steam_64_bit'	=> Steam::to64Bit($profile->small_id),
				'vac'			=> $profile->vac,
				'vac_banned_on'	=> $vacBanDate->format("M j Y"),
				'community'		=> $profile->community,
				'trade'			=> $profile->trade,
				'site_admin'	=> $profile->site_admin?:0,
				'donation'		=> $profile->donation?:0,
				'beta'			=> $profile->beta?:0,
				'times_added'	=> [
					'number' => $profile->total?:0,
					'time' => (new DateTime($profile->created_at))->format("M j Y")
				],
			];
		}

		$multiProfile = new MultiProfile($profilesParsed);
		$profilesParsed = $multiProfile->run();

		$return = [
			'title' => 'Search Result',
			'list' => $profilesParsed
		];

		return $return;
	}
}