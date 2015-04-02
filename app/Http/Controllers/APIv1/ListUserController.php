<?php namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\UserList;
use VacStatus\Models\Profile;
use VacStatus\Models\UserListProfile;

use Illuminate\Http\Request;

use VacStatus\Update\CustomList;
use VacStatus\Update\MultiProfile;

use VacStatus\Steam\Steam;

use Validator;
use Input;
use Auth;
use DateTime;

class ListUserController extends Controller
{
	public function addToList()
	{
		$messages = [
			'required' => 'The :attribute field is required.',
			'numeric' => 'The :attribute field is required.',
		];

		$input = Input::all();

		$validator = Validator::make(
			$input, [
				'list_id' => 'required|numeric',
				'profile_id' => 'required|numeric'
			], $messages
		);

		if ($validator->fails())
		{
			return ['error' => $validator->errors()->all()[0]];
		}

		$userListProfile = UserListProfile::where('user_list_id', (int) $input['list_id'])->get();

		if(Auth::user()->unlockUser() <= $userListProfile->count())
		{
			return ['error' => 'You have reached the maximum users in a list'];
		}

		$userListProfile = $userListProfile
			->where('profile_id', (int) $input['profile_id'])
			->where('deleted_at', null)
			->first();

		if(isset($userListProfile->id))
		{
			return ['error' => 'This user already exists on the list!'];
		}

		$profile = Profile::where('id', (int) $input['profile_id'])->first();

		$userListProfile = new UserListProfile;
		$userListProfile->user_list_id = $input['list_id'];
		$userListProfile->profile_id = $input['profile_id'];
		$userListProfile->profile_name = $profile->display_name;
		$userListProfile->profile_description = $input['description'];

		if(!$userListProfile->save())
		{
			return ['error' => 'There was an error trying to add user to list.'];
		}

		$customList = new CustomList(UserList::where('id', $input['list_id'])->first());
		if($customList->error()) return $customList->error();

		return $customList->getCustomList();
	}

	public function deleteFromList()
	{
		$input = Input::all();

		$userListProfile = UserListProfile::where('user_list_id', $input['list_id'])
			->where('profile_id', $input['profile_id'])
			->first();
		
		if(!$userListProfile->delete()) {
			return ['error' => 'There was an error trying to remove user from list.'];
		}

		$customList = new CustomList(UserList::where('id', $input['list_id'])->first());
		if($customList->error()) return $customList->error();

		return $customList->getCustomList();
	}

	public function addManyToList()
	{
		$input = Input::all();

		$search = Steam::parseSearch($input['search']);
		$description = isset($input['description']) && !empty($input['description']) ? $input['description'] : null;

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

		$profiles = Profile::whereIn('small_id', $smallIds)
			->get(['small_id']);

		$profilesParsed = [];

		foreach($smallIds as $smallId)
		{
			$profile = $profiles->where('small_id', $smallId)->first();

			if(is_null($profile))
			{
				$profilesParsed[] = [
					'small_id' => $smallId
				];
				continue;
			}
		}

		$multiProfile = new MultiProfile($profilesParsed);
		$multiProfile->run();

		$listId = (int) $input['list_id'];

		$userList = UserList::where('id', $listId)->first();

		$userListProfiles = UserListProfile::whereIn('profile.small_id', $smallIds)
			->leftjoin('profile', 'profile.id', '=', 'user_list_profile.profile_id')
			->where('user_list_profile.user_list_id', $listId)
			->where('user_list_profile.deleted_at', null)
			->get(['profile.small_id', 'profile.id']);

		$userListProfilesSmallId = [];
		foreach($userListProfiles as $userListProfile)
		{
			$userListProfilesSmallId[] = $userListProfile->small_id;
		}

		$smallIds = array_diff($smallIds, $userListProfilesSmallId);
		$profiles = Profile::whereIn('small_id', $smallIds)->get();

		$toAddtoList = [];
		foreach($profiles as $profile)
		{
			$toAddtoList[] = new UserListProfile([
       			"profile_id" => $profile->id,
				"profile_name" => $profile->display_name,
				"profile_description" => $description
       		]);
		}

		$userList->UserListProfile()->saveMany($toAddtoList);

		$customList = new CustomList(UserList::where('id', $listId)->first());
		if($customList->error()) return $customList->error();

		return $customList->getCustomList();
	}
}
