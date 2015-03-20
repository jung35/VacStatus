<?php namespace VacStatus\Http\Controllers\APIv1;

use Illuminate\Http\Request;

use VacStatus\Http\Controllers\Controller;
use VacStatus\Http\Requests;

use VacStatus\Models\UserList;
use Auth;

class SettingsController extends Controller
{
	public function subscribeIndex()
	{
		$this->middleware('auth');

		$user = Auth::User();

		$userMail = $user->UserMail;
		$subscription = $user->Subscription()->get(['user_list_id']);

		$userListIds = [];
		foreach($subscription as $getId)
		{
			$userListIds[] = $getId->user_list_id;
		}

		$userLists = UserList::whereIn('user_list.id', $userListIds)
			->leftjoin('users', 'users.id', '=', 'user_list.user_id')
			->whereNull('deleted_at')
			->get([
				'user_list.id',
				'user_list.title',
				
				'users.display_name',
				'users.site_admin',
				'users.donation',
				'users.beta',
			]);

		return compact('userMail', 'userLists');
	}
}