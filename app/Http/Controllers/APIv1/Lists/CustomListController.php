<?php
namespace VacStatus\Http\Controllers\APIv1\Lists;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\Profile;
use VacStatus\Models\Subscription;
use VacStatus\Models\UserList;
use VacStatus\Models\UserListProfile;

use VacStatus\Update\CustomList;
use VacStatus\Update\MultiProfile;

use VacStatus\Steam\Steam;

use Auth;
use DB;
use Input;
use Validator;

class CustomListController extends Controller
{
	private $messages = [
		'required' => 'The :attribute field is required.',
		'numeric' => 'The :attribute field is required.',
		'max' => 'List Name is limited to :max characters.',
	];

	private $listValidationParam = [
		'title' => 'required|max:30',
		'privacy' => 'required|numeric'
	];

	private $addUserValidationParam = [
		'list_id' => 'required|numeric',
		'profile_id' => 'required|numeric'
	];

	private $errorMessages = [
		'list_no_permission_edit' => 'You do not have permission to edit this list',
		'list_reached_max' => 'You have reached the limit of list you can create!',
		'list_save_error' => 'There was an error while trying to save the list.',
		'list_delete_error' => 'There was an error trying to delete the list',
		'sub_reached_max' => 'You have reached the maximum subsciption limit!',
		'sub_already_subbed' => 'You are already subscribed to the list.',
		'user_reached_max' => 'You have reached the maximum users in a list.',
		'user_already_exists' => 'This user already exists on the list!',
		'user_save_error' => 'There was an error trying to add user to list.',
		'user_delete_error' => 'There was an error trying to remove user from list.',
		'search_invalid' => 'Invalid search option.',
	];

	public function get(UserList $userList)
	{
		if(!isset($userList->id)) {
			return ['error' => '404'];
		}

		$customList = new CustomList($userList);
		if($customList->error()) return $customList->error();
		
		return $customList->getCustomList();
	}

	public function modify($listId = null)
	{
		$validator = Validator::make(Input::all(), $this->listValidationParam, $this->messages);

		if ($validator->fails()) return ['error' => $validator->errors()->all()[0]];

		$user = Auth::user();

		if(!is_null($listId))
		{
			$userList = UserList::where('id', $listId)->first();
			if($userList->user_id !== $user->id) return $this->error('list_no_permission_edit');
		}
		elseif(!$user->canMakeList()) return $this->error('list_reached_max');
		else $userList = new UserList;

		$userList->title = Input::get('title');
		$userList->privacy = Input::get('privacy');

		if(!is_null($listId) && !$userList->save() || !$user->UserList()->save($userList)) 
		{
			return $this->error('list_save_error');
		}
		
		return $this->getMyListsDetailed($user->id);
	}

	public function delete(UserList $userList)
	{
		if($userList->user_id !== Auth::user()->id) return $this->error('forbidden');
		
		$userList->UserListProfile()->delete();
		
		if(!$userList->delete()) return $this->error('list_delete_error');

		return [true];
	}

	public function subscribe(UserList $userList)
	{
		if(!isset($userList->id)) return $this->error('404');

		$user = Auth::user();

		if($user->unlockSubscription() <= $user->Subscription()->count())
		{
			return $this->error('sub_reached_max');
		}

		$subscription = Subscription::whereUserId($user->id)
			->whereUserListId($userList->id)
			->first();

		if(isset($subscription->id)) return $this->error('sub_already_subbed');

		$subscription = new Subscription;
		$subscription->user_id = $user->id;
		$subscription->user_list_id = $userList->id;
		$subscription->save();
		
		return $this->get($userList);
	}

	public function unsubscribe(UserList $userList)
	{
		if(!isset($userList->id)) return $this->error('404');

		$user = Auth::user();

		$subscription = Subscription::whereUserListId($userList->id)
			->whereUserId($user->id)->delete();
		
		return $this->get($userList);
	}

	public function addProfileToList()
	{
		$input = Input::all();

		$validator = Validator::make($input, $this->addUserValidationParam, $this->messages);
		if ($validator->fails()) return $this->error($validator->errors()->all()[0]);

		$userListProfile = UserListProfile::where('user_list_id', (int) $input['list_id'])->get();

		if(Auth::user()->unlockUser() <= $userListProfile->count())
		{
			return $this->error('user_reached_max');
		}

		$userListProfile = $userListProfile
			->where('profile_id', (int) $input['profile_id'])
			->where('deleted_at', null)
			->first();

		if(isset($userListProfile->id)) return $this->error('user_already_exists');

		$profile = Profile::where('id', (int) $input['profile_id'])->first();

		$userListProfile = new UserListProfile;
		$userListProfile->user_list_id = $input['list_id'];
		$userListProfile->profile_id = $input['profile_id'];
		$userListProfile->profile_name = $profile->display_name;
		$userListProfile->profile_description = $input['description'];

		if(!$userListProfile->save()) return $this->error('user_save_error');

		return $this->get(UserList::where('id', $input['list_id'])->first());
	}

	public function deleteProfileFromList()
	{
		$input = Input::all();

		$userListProfile = UserListProfile::where('user_list_id', $input['list_id'])
			->where('profile_id', $input['profile_id'])
			->first();
		
		if(!isset($userListProfile->id) || !$userListProfile->delete())
		{
			return $this->error('user_delete_error');
		}

		return $this->get(UserList::where('id', $input['list_id'])->first());
	}

	public function addManyProfilesToList()
	{
		$input = Input::all();

		$smallIds = $this->findValidProfiles(Steam::parseSearch($input['search']));
		if(isset($smallIds['error'])) return $smallIds;

		$this->updateValidProfiles($smallIds);

		$listId = (int) $input['list_id'];
		$userList = UserList::where('id', $listId)->first();

		$userListProfiles = UserListProfile::whereIn('profile.small_id', $smallIds)
			->where('user_list_profile.user_list_id', $listId)
			->whereNull('user_list_profile.deleted_at')
			->leftjoin('profile', 'profile.id', '=', 'user_list_profile.profile_id')
			->get(['profile.small_id']);

		$smallIds = array_diff($smallIds, $userListProfiles->lists('small_id')->toArray());
		$profiles = Profile::whereIn('small_id', $smallIds)->get();

		$toAddtoList = [];

		$totalCurrentProfiles = $userList->UserListProfile()->count();
		$description = isset($input['description']) && !empty($input['description']) ? $input['description'] : null;

		foreach($profiles as $k => $profile)
		{
			if(Auth::user()->unlockUser() <= $totalCurrentProfiles + $k) break;

			$toAddtoList[] = new UserListProfile([
				"profile_id" => $profile->id,
				"profile_name" => $profile->display_name,
				"profile_description" => $description
			]);
		}

		$userList->UserListProfile()->saveMany($toAddtoList);
		return $this->get($userList);
	}

	private function error($message)
	{
		$errorMessage = $this->errorMessages[$message];
		if($errorMessage) return ['error' => $errorMessage];

		return ['error' => $message];
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

	private function findValidProfiles($search)
	{
		if(!is_array($search)) return $this->error('search_invalid');

		$validProfile = (new SteamUser($search))->fetch();

		if(!is_array($validProfile)) return $this->error($validProfile);

		return Steam::toSmallId($validProfile);
	}

	private function updateValidProfiles($smallIds)
	{
		$profiles = Profile::whereIn('small_id', $smallIds)
			->get(['small_id']);

		$profilesParsed = [];

		foreach($smallIds as $smallId)
		{
			$profile = $profiles->where('small_id', $smallId)->first();

			if(is_null($profile))
			{
				$profilesParsed[] = ['small_id' => $smallId];
				continue;
			}
		}

		$multiProfile = new MultiProfile($profilesParsed);
		$multiProfile->run();
	}
}