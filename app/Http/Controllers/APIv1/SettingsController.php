<?php

namespace VacStatus\Http\Controllers\APIv1;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\User;
use VacStatus\Models\UserList;
use VacStatus\Models\UserMail;

use Auth;
use Input;
use Validator;
use Mail;

class SettingsController extends Controller
{
	public function subscribeIndex()
	{
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

	public function makeSubscription()
	{
		if(Input::has('_key')) return ['error' => 'forbidden'];

		$email = Input::get('email');
		$pushBullet = Input::get('push_bullet');

		$user = Auth::user();
		$userMail = $user->UserMail;

		if(!isset($userMail->id))
		{
			$userMail = new UserMail;
			$userMail->user_id = $user->id;
		}

		$sendVerificationTo = [];

		$rules = array('email' => 'email');

		if($email)
		{
			$validator = Validator::make(['email' => $email], $rules);

			if(!$validator->fails() && $userMail->email != $email)
			{
				$verificationCode = str_random(32);

				$sendVerificationTo[] = [
					'email' => $email,
					'verify' => $verificationCode
				];

				$userMail->email = $email;
				$userMail->verify = $verificationCode;
			}
		}
		
		if($pushBullet)
		{
			$validator = Validator::make(['email' => $pushBullet], $rules);

			if(!$validator->fails() && $userMail->pushbullet != $pushBullet)
			{
				$verificationCode = str_random(32);

				$sendVerificationTo[] = [
					'email' => $pushBullet,
					'verify' => $verificationCode
				];

				$userMail->pushbullet = $pushBullet;
				$userMail->pushbullet_verify = $verificationCode;
			}
		}

		if(count($sendVerificationTo) == 0) return $this->subscribeIndex();

		if(!$userMail->save()) return ['error' => 'There was an error trying to save the emails'];

		$appURL = env('APP_URL');
		foreach($sendVerificationTo as $data) {
			Mail::send('emails.verification', [
				'url' => "$appURL/settings/" . $data['email'] . "/" . $data['verify'],
			], function($message) use ($data) {
				$message->to($data['email'])->subject('Thank you for subscribing!');
			});
		}

		return $this->subscribeIndex();
	}

	public function deleteEmail()
	{
		if(Input::has('_key')) return ['error' => 'forbidden'];

		$user = Auth::user();
		$userMail = $user->UserMail;

		$userMail->email = "";
		$userMail->verify = "";

		$userMail->save();

		return $this->subscribeIndex();
	}

	public function deletePushBullet()
	{
		if(Input::has('_key')) return ['error' => 'forbidden'];

		$user = Auth::user();
		$userMail = $user->UserMail;

		$userMail->pushbullet = "";
		$userMail->pushbullet_verify = "";

		$userMail->save();

		return $this->subscribeIndex();
	}

	public function showUserKey()
	{
		$user = Auth::user();

		return ['key' => $user->user_key];
	}

	public function newUserKey()
	{
		if(Input::has('_key')) return ['error' => 'forbidden'];

		$user = Auth::user();

		$users = User::whereNotNull('user_key')->get();
		while(true) {
			$userKey = str_random(32);
			$exist = $users->where('user_key', $userKey)->first();
			if(!isset($exist->id)) {
				$user->user_key = $userKey;
				$user->save();
				break;
			}
		}

		return ['key' => $user->user_key];
	}

	public function subscriptionVerify($email, $code)
	{
		$userMail = UserMail::whereRaw('(email = ? and verify = ?) or (pushbullet = ? and pushbullet_verify = ?)', [
			$email, $code, $email, $code
		])->first();

		if(!isset($userMail->id)) return 'error';

		if($userMail->email == $email && $userMail->verify == $code)
		{
			$userMail->verify = "verified";
		}
		else if($userMail->pushbullet == $email && $userMail->pushbullet_verify == $code)
		{
			$userMail->pushbullet_verify = "verified";
		}

		$userMail->save();
		return 'success';
	}
}