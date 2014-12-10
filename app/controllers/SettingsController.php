<?php

class SettingsController extends \BaseController {

    public function showSettings()
    {
        $userMail = Auth::User()->UserMail;
        $emailStatus = false;
        $email = '';
        if($userMail != null && $userMail->canMail()) {
            $emailStatus = true;
            $email = $userMail->email;
        }
        return View::make('settings/addEmail', array(
                          'emailStatus' => $emailStatus,
                          'email' => $email));
    }

    public function editSettings()
    {
        $email = Input::get('email');
        $validator = Validator::make(
            array('email' => $email),
            array('email' => 'required|email')
        );

        if($validator->fails()) {
            return Redirect::back()->with('error', $validator->messages()->first('email'));
        }

        $userMail = UserMail::whereUserId(Auth::User()->id)->first();

        $userMailCheck = UserMail::whereEmail($email)->first();

        if($userMail === null)
        {
            $userMail = new UserMail;
            $userMail->user_id = Auth::User()->id;
        } else if(!is_null($userMailCheck) && $userMailCheck->email == $email) {
            return Redirect::back()->with('error', 'This email is either currently subscribed or waiting for verification.');
        }

        $userMail->email = $email;
        $userMail->verify = str_random(32);

        if($userMail->save()) {
            Mail::send('emails.verification', array('verify' => $userMail->verify), function($message) use ($email)
            {
                $message->to($email)->subject('Thank you for subscribing!');
            });
            return Redirect::back()->with('success', 'Please check your email for verification url!');
        }
        return Redirect::back()->with('error', 'Something went wrong sending the email.');
    }

    public function verifySettings($verification) {
        $userMail = UserMail::whereVerify($verification)->whereUserId(Auth::User()->id)->first();

        if($userMail == null) {
            return Redirect::route('settings')->with('error', 'Error verifying.');
        }

        if($userMail->canMail()) {
            return Redirect::route('settings')->with('success', 'You are already verified.');
        }

        $userMail->verify = 'verified';
        if($userMail->save()) {
            return Redirect::route('settings')->with('success', 'You have been successfuly verified.');
        }

        return Redirect::route('settings')->with('error', 'There was an error trying to verify. Please try again');
    }
}
