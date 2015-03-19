<?php namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use Illuminate\Http\Request;

use VacStatus\Update\MostTracked;
use VacStatus\Update\LatestTracked;
use VacStatus\Update\CustomList;

use VacStatus\Models\User;
use VacStatus\Models\UserList;
use VacStatus\Models\Subscription;

use VacStatus\Steam\Steam;

use Session;

class ListController extends Controller
{
	public function listList()
	{
		$return = [
			'my_list' => [],
			'friends_list' => []
		];

		$userId = \Auth::user()->id;
		$myLists = UserList::where('user_list.user_id', $userId)
			->leftjoin('user_list_profile as ulp_1', 'ulp_1.user_list_id', '=', 'user_list.id')
			->leftjoin('subscription', 'subscription.user_list_id', '=', 'user_list.id')
			->groupBy('user_list.id')
			->orderBy('user_list.id', 'desc')
			->get([
				'user_list.id',
				'user_list.title',
				'user_list.privacy',
				'user_list.created_at',
				
				\DB::raw('count(ulp_1.id) as users_in_list'),
				\DB::raw('count(distinct subscription.id) as sub_count'),
			]);

		foreach($myLists as $myList)
		{
			$return['my_list'][] = [
				'id' => $myList->id,
				'title' => $myList->title,
				'privacy' => $myList->privacy,
				'created_at' => $myList->created_at->format("M j Y"),
				
				'users_in_list' => $myList->users_in_list,
				'sub_count' => $myList->sub_count,
			];
		}

		if(Session::has('friendsList'))
		{
			$friendsList = Session::get('friendsList');

			$myfriendsLists = User::whereIn('users.small_id', $friendsList)
				->whereNotIn('user_list.privacy', [3])
				->groupBy('user_list.id')
				->orderBy('user_list.id', 'desc')
				->leftjoin('user_list', 'user_list.user_id', '=', 'users.id')
				->leftjoin('user_list_profile', 'user_list.id', '=', 'user_list_profile.user_list_id')
				->leftjoin('profile', 'profile.small_id', '=', 'users.small_id')
				->leftjoin('subscription', 'subscription.user_list_id', '=', 'user_list.id')
				->having('users_in_list', '>', 0)
				->get([
					'profile.id as profile_id',
					'profile.display_name',
					'profile.avatar_thumb',
					'profile.small_id',

					'user_list.id as user_list_id',
					'user_list.title',
					'user_list.privacy',
					'user_list.created_at',
					
					\DB::raw('count(user_list_profile.created_at) as users_in_list'),
					\DB::raw('count(Distinct subscription.id) as sub_count'),
				]);


			foreach($myfriendsLists as $myfriendsList)
			{
				$return['friends_list'][] = [
					'profile_id' => $myfriendsList->profile_id,
					'display_name' => $myfriendsList->display_name,
					'avatar_thumb' => $myfriendsList->avatar_thumb,
					'steam_64_bit' => Steam::to64bit($myfriendsList->small_id),

					'user_list_id' => $myfriendsList->user_list_id,
					'title' => $myfriendsList->title,
					'privacy' => $myfriendsList->privacy,
					'created_at' => $myfriendsList->created_at->format("M j Y"),

					'users_in_list' => $myfriendsList->users_in_list,
					'sub_count' => $myfriendsList->sub_count,
				];
			}
		}

		return $return;
	}

	public function mostTracked()
	{
		$mostTracked = new MostTracked;

		$return = [
			'title' => 'Most Tracked Users',	
			'list' => $mostTracked->getMostTracked()
		];


		return $return;
	}

	public function latestTracked()
	{
		$latestTracked = new LatestTracked();

		$return = [
			'title' => 'Latest Tracked Users',
			'list' => $latestTracked->getLatestTracked()
		];

		return $return;
	}

	public function customList(UserList $userList)
	{
		if(!isset($userList->id)) {
			return ['error' => '404'];
		}
		$customList = new CustomList($userList);
		if($customList->error()) return $customList->error();
		
		return $customList->getCustomList();
	}
}
