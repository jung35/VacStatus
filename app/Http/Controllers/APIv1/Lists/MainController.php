<?php
namespace VacStatus\Http\Controllers\APIv1\Lists;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\User;
use VacStatus\Models\UserList;

use VacStatus\Steam\Steam;

use DB;
use Auth;

class MainController extends Controller
{
	public function myLists()
	{
		if(!Auth::check()) return [];

		$myList = UserList::where('user_id', Auth::user()->id)
			->orderBy('id', 'desc')
			->get([
				'id',
				'title',
				'privacy',
			]);

		return $myList;
	}

	public function listPortal()
	{
		$return = [
			'my_list' => [],
			'friends_list' => []
		];

		if(!Auth::check()) return $return;

		$user = Auth::user();

		$return['my_list'] = $this->getMyListsDetailed($user->id);

		if(isset($user->friendslist))
		{
			$return['friends_list'] = $this->getManyListsDetailed(json_decode($user->friendslist));
		}

		return $return;
	}

	private function getMyListsDetailed($userId)
	{
		$return = [];

		$myLists = UserList::where('user_list.user_id', $userId)
			->groupBy('user_list.id')
			->orderBy('user_list.id', 'desc')
			->leftJoin('user_list_profile', function($join)
			{
				$join->on('user_list_profile.user_list_id', '=', 'user_list.id')
					->whereNull('user_list_profile.deleted_at');
			})
			->leftJoin('subscription', function($join)
			{
				$join->on('subscription.user_list_id', '=', 'user_list.id')
					->whereNull('subscription.deleted_at');
			})
			->get([
				'user_list.id',
				'user_list.title',
				'user_list.privacy',
				'user_list.created_at',
				
				DB::raw('count(DISTINCT user_list_profile.profile_id) as users_in_list'),
				DB::raw('count(DISTINCT subscription.id) as sub_count'),
			]);

		return $myLists;
	}

	private function getManyListsDetailed($smallIds)
	{
		$return = [];

		$myfriendsLists = User::whereIn('users.small_id', $smallIds)
			->whereNotIn('user_list.privacy', [3])
			->whereNull('user_list_profile.deleted_at')
			->groupBy('user_list.id')
			->orderBy('user_list.id', 'desc')
			->leftjoin('user_list', 'user_list.user_id', '=', 'users.id')
			->leftjoin('user_list_profile', 'user_list.id', '=', 'user_list_profile.user_list_id')
			->leftjoin('profile', 'profile.small_id', '=', 'users.small_id')
			->leftJoin('subscription', function($join)
			{
				$join->on('subscription.user_list_id', '=', 'user_list.id')
					->whereNull('subscription.deleted_at');
			})->having('users_in_list', '>', 0)
			->get([
				'profile.id as profile_id',
				'profile.display_name',
				'profile.avatar_thumb',
				'profile.small_id',

				'user_list.id as user_list_id',
				'user_list.title',
				'user_list.privacy',
				'user_list.created_at',

				'users.site_admin',
				'users.donation',
				'users.beta',
				
				DB::raw('count(DISTINCT user_list_profile.profile_id) as users_in_list'),
				DB::raw('count(DISTINCT subscription.id) as sub_count'),
			]);


		foreach($myfriendsLists as $myfriendsList)
		{
			if(!isset($myfriendsList->profile_id) || empty($myfriendsList->profile_id)) continue;

			$return[] = [
				'profile_id'	=> $myfriendsList->profile_id,
				'display_name'	=> $myfriendsList->display_name,
				'avatar_thumb'	=> $myfriendsList->avatar_thumb,
				'steam_64_bit'	=> Steam::to64bit($myfriendsList->small_id),

				'user_list_id'	=> $myfriendsList->user_list_id,
				'title'			=> $myfriendsList->title,
				'privacy'		=> $myfriendsList->privacy,
				'created_at'	=> $myfriendsList->created_at->format("M j Y"),
				
				'site_admin'	=> (int) $myfriendsList->site_admin?:0,
				'donation'		=> (int) $myfriendsList->donation?:0,
				'beta'			=> (int) $myfriendsList->beta?:0,

				'users_in_list'	=> $myfriendsList->users_in_list,
				'sub_count'		=> $myfriendsList->sub_count,
			];
		}

		return $return;
	}
}