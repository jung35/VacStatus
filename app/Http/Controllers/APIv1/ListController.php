<?php namespace VacStatus\Http\Controllers\APIv1;

use Illuminate\Http\Request;

use VacStatus\Http\Controllers\Controller;
use VacStatus\Http\Requests;
use VacStatus\Update\MostTracked;
use VacStatus\Update\LatestTracked;
use VacStatus\Update\CustomList;
use VacStatus\Update\LatestVAC;

use VacStatus\Models\User;
use VacStatus\Models\UserList;
use VacStatus\Models\Subscription;

use VacStatus\Steam\Steam;

use Cache;
use Validator;
use Input;
use Auth;

class ListController extends Controller
{
	public function mySimpleList()
	{
		$myList = UserList::where('user_id', Auth::user()->id)
			->orderBy('id', 'desc')
			->get([
				'id',
				'title',
				'privacy',
			]);

		return $myList;
	}
	public function listList()
	{
		$return = [
			'my_list' => [],
			'friends_list' => []
		];

		$user = Auth::user();

		$myLists = UserList::where('user_list.user_id', $user->id)
			->leftjoin('user_list_profile as ulp_1', 'ulp_1.user_list_id', '=', 'user_list.id')
			->groupBy('user_list.id')
			->orderBy('user_list.id', 'desc')
			->leftJoin('subscription', function($join)
			{
				$join->on('subscription.user_list_id', '=', 'user_list.id')
					->whereNull('subscription.deleted_at');
			})->whereNull('ulp_1.deleted_at')
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
				'id'			=> $myList->id,
				'title'			=> $myList->title,
				'privacy'		=> $myList->privacy,
				'created_at'	=> $myList->created_at->format("M j Y"),
				
				'users_in_list'	=> $myList->users_in_list,
				'sub_count'		=> $myList->sub_count,
			];
		}

		if(isset($user->friendslist))
		{
			$friendsList = json_decode($user->friendslist);

			$myfriendsLists = User::whereIn('users.small_id', $friendsList)
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
					
					\DB::raw('count(user_list_profile.created_at) as users_in_list'),
					\DB::raw('count(Distinct subscription.id) as sub_count'),
				]);


			foreach($myfriendsLists as $myfriendsList)
			{
				if(!isset($myfriendsList->profile_id) || empty($myfriendsList->profile_id)) continue;
				$return['friends_list'][] = [
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

	public function latestVAC()
	{
		$latestVac = new LatestVAC();

		$return = [
			'title' => 'Latest VAC Banned Users',
			'list' => $latestVac->getLatestVAC()
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

	public function modifyCustomList($listId = null)
	{
		$messages = [
			'required' => 'The :attribute field is required.',
			'numeric' => 'The :attribute field is required.',
			'max' => 'List Name is limited to :max characters.',
		];

		$validator = Validator::make(
			Input::all(), [
				'title' => 'required|max:30',
				'privacy' => 'required|numeric'
			], $messages
		);

		if ($validator->fails())
		{
			return ['error' => $validator->errors()->all()[0]];
		}

		$user = Auth::user();

		if(!is_null($listId))
		{
			$userList = UserList::where('id', $listId)->first();
			
			if($userList->user_id !== $user->id)
			{
				return ['error' => 'You do not have permission to edit this list'];
			}
		} else if(!$user->canMakeList())
		{
			return ['error' => 'You\'ve reached the limit of list you can create!'];
		} else {
			$userList = new UserList;
		}

		$userList->title = Input::get('title');
		$userList->privacy = Input::get('privacy');

		if(!is_null($listId) && !$userList->save() || !$user->UserList()->save($userList))
		{
			return ['error' => 'There was an error while trying to save the list.'];
		}

		$myLists = UserList::where('user_list.user_id', $user->id)
			->leftjoin('user_list_profile as ulp_1', 'ulp_1.user_list_id', '=', 'user_list.id')
			->groupBy('user_list.id')
			->orderBy('user_list.id', 'desc')
			->leftJoin('subscription', function($join)
			{
				$join->on('subscription.user_list_id', '=', 'user_list.id')
					->whereNull('subscription.deleted_at');
			})->whereNull('ulp_1.deleted_at')
			->get([
				'user_list.id',
				'user_list.title',
				'user_list.privacy',
				'user_list.created_at',
				
				\DB::raw('count(ulp_1.id) as users_in_list'),
				\DB::raw('count(distinct subscription.id) as sub_count'),
			]);

		$return = [];

		foreach($myLists as $myList)
		{
			$return[] = [
				'id'			=> $myList->id,
				'title'			=> $myList->title,
				'privacy'		=> $myList->privacy,
				'created_at'	=> $myList->created_at->format("M j Y"),
				
				'users_in_list'	=> $myList->users_in_list,
				'sub_count'		=> $myList->sub_count,
			];
		}

		return $return;
	}

	public function deleteCustomList(UserList $userList)
	{
		if($userList->user_id !== Auth::user()->id) return ['error' => 'forbidden'];
		
		$userList->UserListProfile()->delete();
		
		if(!$userList->delete()) {
			return ['error' => 'There was an error trying to delete the list'];
		}

		return [true];
	}

	public function listSubscribe(UserList $userList)
	{
		if(!isset($userList->id)) {
			return ['error' => '404'];
		}

		$user = Auth::user();

		if($user->unlockSubscription() <= $user->Subscription()->count())
		{
			return ['error' => 'You have reached the maximum subsciption limit!'];
		}

		$subscription = new Subscription;
		$subscription->user_id = $user->id;
		$subscription->user_list_id = $userList->id;
		$subscription->save();

		$customList = new CustomList($userList);
		if($customList->error()) return $customList->error();
		
		return $customList->getCustomList();
	}

	public function listUnsubscribe(UserList $userList)
	{
		if(!isset($userList->id)) {
			return ['error' => '404'];
		}

		$user = Auth::user();

		$subscription = Subscription::whereUserListId($userList->id)
			->whereUserId($user->id)
			->first();

		$subscription->delete();
		
		$customList = new CustomList($userList);
		if($customList->error()) return $customList->error();
		
		return $customList->getCustomList();
	}
}
