<?php

class SettingsController extends \BaseController {

    public function showSettings()
    {
        $userMail = Auth::User()->UserMail;
       
        $emailStatus = false;
        $email = '';
       
        $pbemailStatus = false;
        $pbemail = '';
       
        if($userMail != null && $userMail->canMail()) {
            $emailStatus = true;
            $email = $userMail->email;
        }
       
        if($userMail != null && $userMail->canPushbullet()) {
            $pbemailStatus = true;
            $pbemail = $userMail->pushbullet;
        }
       
        return View::make('settings/addEmail', array(
                          'emailStatus' => $emailStatus,
                          'email' => $email,
                          'pbemailStatus' => $pbemailStatus,
                          'pbemail' => $pbemail));
    }

    public function editSettings()
    {
        $email = Input::get('email');
        $pbemail = Input::get('pbemail');
        
        if (!is_null($email) && $email != "") {
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
                Mail::send('emails.verification', array('type' => 'email', 'verify' => $userMail->verify), function($message) use ($email)
                {
                    $message->to($email)->subject('Thank you for subscribing!');
                });
                return Redirect::back()->with('success', 'Please check your email for verification url!');
            }
            return Redirect::back()->with('error', 'Something went wrong sending the email.');
        }
        
        if (!is_null($pbemail) && $pbemail != "") {
            $validator = Validator::make(
                array('pbemail' => $pbemail),
                array('pbemail' => 'required|email')
            );

            if($validator->fails()) {
                return Redirect::back()->with('error', $validator->messages()->first('pbemail'));
            }

            $userMail = UserMail::whereUserId(Auth::User()->id)->first();

            $userMailCheck = UserMail::wherePushbullet($pbemail)->first();

            if($userMail === null)
            {
                $userMail = new UserMail;
                $userMail->user_id = Auth::User()->id;
            } else if(!is_null($userMailCheck) && $userMailCheck->pushbullet == $pbemail) {
                return Redirect::back()->with('error', 'This email is either currently subscribed or waiting for verification.');
            }

            $userMail->pushbullet = $pbemail;
            $userMail->pushbullet_verify = str_random(32);

            if($userMail->save()) {
                Mail::send('emails.verification', array('type' => 'pb', 'verify' => $userMail->pushbullet_verify), function($message) use ($pbemail)
                {
                    $message->to($pbemail)->subject('Thank you for subscribing!');
                });
                return Redirect::back()->with('success', 'Please check your email for verification url!');
            }
            return Redirect::back()->with('error', 'Something went wrong sending the email.');
        }
        return Redirect::back();
    }

    public function verifySettings($verification, $type) {
        if ($type == "email") {
            $userMail = UserMail::whereVerify($verification)->whereUserId(Auth::User()->id)->first();

            if($userMail == null) {
                return Redirect::route('settings')->with('error', 'Error verifying your Email.');
            }

            if($userMail->canMail()) {
                return Redirect::route('settings')->with('success', 'Your Email has already been verified.');
            }

            $userMail->verify = 'verified';
            if($userMail->save()) {
                return Redirect::route('settings')->with('success', 'Your Email has been successfuly verified.');
            }

            return Redirect::route('settings')->with('error', 'There was an error trying to verify your Email. Please try again.');
        } else if ($type == "pb") {
            $userMail = UserMail::wherePushbullet_verify($verification)->whereUserId(Auth::User()->id)->first();

            if($userMail == null) {
                return Redirect::route('settings')->with('error', 'Error verifying your PushBullet account.');
            }

            if($userMail->canPushbullet()) {
                return Redirect::route('settings')->with('success', 'Your PushBullet account has already been verified.');
            }

            $userMail->pushbullet_verify = 'verified';
            if($userMail->save()) {
                return Redirect::route('settings')->with('success', 'Your PushBullet account has been successfuly verified.');
            }

            return Redirect::route('settings')->with('error', 'There was an error trying to verify your PushBullet acount. Please try again.');
        } else return Redirect::route('settings')->with('error', 'Invalid verification type.');
    }
}