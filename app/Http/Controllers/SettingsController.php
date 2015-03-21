<?php namespace VacStatus\Http\Controllers;

use VacStatus\Http\Requests;
use VacStatus\Http\Controllers\Controller;
use VacStatus\Models\UserMail;

use Auth;

class SettingsController extends Controller
{
	public function subscriptionPage()
	{
		return view('settings/subscription');
	}

	public function subscriptionVerify($email, $verifyKey)
	{
		$userMail = UserMail::whereRaw('(email = ? and verify = ?) or (pushbullet = ? and pushbullet_verify = ?)', [
   			$email, $verifyKey, $email, $verifyKey
   			])->first();

		if(!isset($userMail->id))
		{
			if(Auth::check()) return redirect()->route('settings');
			return redirect()->route('home');
		}

		if($userMail->email == $email && $userMail->verify == $verifyKey)
		{
			$userMail->verify = "verified";
		} else if($userMail->pushbullet == $email && $userMail->pushbullet_verify == $verifyKey)
		{
			$userMail->pushbullet_verify = "verified";
		}

		$userMail->save();
		if(Auth::check()) return redirect()->route('settings');
		return redirect()->route('home');
	}
}