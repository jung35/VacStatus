<?php namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\UserList;
use VacStatus\Models\Profile;
use VacStatus\Models\UserListProfile;

use Illuminate\Http\Request;

use VacStatus\Update\CustomList;

use Validator;
use Input;
use Auth;

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
}
