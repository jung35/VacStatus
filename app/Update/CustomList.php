<?php namespace VacStatus\Update;

use Cache;
use Carbon;
use DateTime;
use Auth;

use VacStatus\Models\User;
use VacStatus\Models\UserList;
use VacStatus\Models\UserListProfile;

use VacStatus\Steam\Steam;

class CustomList
{
	private $userList;
	private $error = null;

	function __construct($userList)
	{
		if(!isset($userList->id))
		{
			$this->error = "list_invalid";
			return;
		}

		if(Auth::check()) 
		{
			$user = Auth::User();
			$userFriends = json_decode($user->friendslist);


			if($user->id != $userList->user_id)
			{
				$listAuthor = User::whereId($userList->user_id)->first();
				if(($listAuthor->exists() && !empty($userFriends) && !in_array($listAuthor->small_id, $userFriends) && $userList->privacy == 2) || $userList->privacy == 3)
				{
					$this->error = "list_no_permission";
					return;
				}
			}

		} else if($userList->privacy == 2 || $userList->privacy == 3)
		{
			$this->error = "list_no_permission";
			return;
		}

		$this->userList = $userList;
	}

	public function error()
	{
		return is_null($this->error) ? false : ['error' => $this->error];
	}

	public function myList()
	{
		if(Auth::check() && $this->userList->user_id == Auth::user()->id) return true;

		return false;
	}

	public function getCustomList()
	{
		if($this->error()) return $this->error();
		$userList = $this->userList;

		$userListProfiles = UserList::where('user_list.id', $userList->id)
			->leftjoin('user_list_profile as ulp_1', 'ulp_1.user_list_id', '=', 'user_list.id')
			->whereNull('ulp_1.deleted_at')
			->leftJoin('user_list_profile as ulp_2', function($join)
			{
				$join->on('ulp_2.profile_id', '=', 'ulp_1.profile_id')
					->whereNull('ulp_2.deleted_at');
			})
			->leftjoin('profile', 'ulp_1.profile_id', '=', 'profile.id')
			->leftjoin('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
			->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
			->leftJoin('subscription', function($join)
			{
				$join->on('subscription.user_list_id', '=', 'user_list.id')
					->whereNull('subscription.deleted_at');
			})->groupBy('profile.id')
			->orderBy('ulp_1.id', 'desc')
			->get([
		      	'ulp_1.profile_name',
		      	'ulp_1.profile_description',
		      	'ulp_1.created_at as added_at',

				'profile.id',
				'profile.display_name',
				'profile.avatar_thumb',
				'profile.small_id',

				'profile_ban.vac_bans',
				'profile_ban.game_bans',
				'profile_ban.last_ban_date',
				'profile_ban.community',
				'profile_ban.trade',

				'users.site_admin',
				'users.donation',
				'users.beta',

				\DB::raw('max(ulp_1.created_at) as created_at'),
				\DB::raw('count(ulp_2.profile_id) as total'),
				\DB::raw('count(distinct subscription.id) as sub_count'),
			]);


		$canSub = false;
		$subscription = null;

		if(Auth::check())
		{
			$user = Auth::user();
			$userMail = $user->UserMail;
			$subscription = $user->Subscription
				->where('user_list_id', $userList->id)
				->first();
			if($userMail &&
			   ($userMail->verify == "verified" || $userMail->pushbullet_verify == "verified")) $canSub = true;
		}

		$return = [
			'list_info' => [
				'id'			=> $userList->id,
				'title'			=> $userList->title,
				'author'		=> $userList->user->display_name,
				'my_list'		=> $this->myList(),
				'can_sub'		=> $canSub,
				'subscription'	=> $subscription,
				'privacy'		=> $userList->privacy,
				'sub_count'		=> isset($userListProfiles[0]) ? $userListProfiles[0]->sub_count : 0,
			],
			'profiles'			=> [],
		];

		foreach($userListProfiles as $userListProfile)
		{
			if(is_null($userListProfile->id)) continue;
			$lastBanDate = new DateTime($userListProfile->last_ban_date);

			$return['profiles'][] = [
				'id'					=> $userListProfile->id,
				'display_name'			=> $userListProfile->profile_name?:$userListProfile->display_name,
				'avatar_thumb'			=> $userListProfile->avatar_thumb,
				'small_id'				=> $userListProfile->small_id,
				'steam_64_bit'			=> Steam::to64Bit($userListProfile->small_id),
				'vac_bans'				=> $userListProfile->vac_bans,
				'game_bans'				=> $userListProfile->game_bans,
				'last_ban_date'			=> $lastBanDate->format("M j Y"),
				'community'				=> $userListProfile->community,
				'trade'					=> $userListProfile->trade,
				'site_admin'			=> (int) $userListProfile->site_admin?:0,
				'donation'				=> (int) $userListProfile->donation?:0,
				'beta'					=> (int) $userListProfile->beta?:0,
				'profile_description'	=> $userListProfile->profile_description,
				'added_at'				=> (new DateTime($userListProfile->added_at))->format("M j Y"),
				'times_added'			=> [
					'number'	=> $userListProfile->total,
					'time'		=> (new DateTime($userListProfile->created_at))->format("M j Y")
				],
			];
		}

		$multiProfile = new MultiProfile($return['profiles'], $userList->id);
		$return['profiles'] = $multiProfile->run();

		return $return;
	}
}