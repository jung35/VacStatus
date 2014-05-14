<?php
class MailController extends BaseController {

  public function showSub()
  {
    $userMail = mailList::whereSteamUserId(Session::get('user.id'))->first();

    $emailSendTime = Session::get('email.send');
    if(!is_null($emailSendTime)) {
      $emailSendTime = time() - $emailSendTime;

      if($emailSendTime > 3600) {
        $emailSendTime = (int)($emailSendTime / 3600). ' hours ago';
      } elseif($emailSendTime > 60) {
        $emailSendTime = (int)($emailSendTime / 60). ' mintues ago';
      } else {
        $emailSendTime .= ' seconds ago';
      }
    }

    return View::make('user.subscribe', array('userMail' => $userMail, 'emailSendTime' => $emailSendTime) );
  }

  public function doSub()
  {
    $userMail = mailList::whereSteamUserId(Session::get('user.id'))->first();

    $unsub = Input::get('unsub');
    if($unsub != null) {

      $this->log->addInfo("UnSubscribe", array(
        "steamUserId" => Session::get("user.id"),
        "displayName" => Session::get("user.name"),
        "ipAddress" => Request::getClientIp()
      ));

      $userMail->delete();
      return Redirect::route('subscribe');
    }

    $setEmail = Input::get('setEmail');
    if($setEmail == null) return;

    $verificationCode = str_random(40);

    if($userMail != null) {
      if($setEmail == $userMail) return;
    } else {
      $userMail = new mailList;
      $userMail->steam_user_id = Session::get('user.id');
    }
    $userMail->email = $setEmail;
    $userMail->verify = $verificationCode;
    $userMail->save();

    Session::remove('email.send');


    $this->log->addInfo("Subscribe", array(
      "steamUserId" => Session::get("user.id"),
      "displayName" => Session::get("user.name"),
      "ipAddress" => Request::getClientIp()
    ));

    $this->sendVerification();
    return Redirect::route('subscribe');
  }

  public function sendVerification()
  {
    if(Session::get('email.send') != null && time() - Session::get('email.send') < 300) {
      return Redirect::route('subscribe');
    }

    $userMail = mailList::whereSteamUserId(Session::get('user.id'))->first();

    if($userMail != null && $userMail->verify != 'done') {
      $email = $userMail->email;

      Mail::send('emails.verification', Array('verify' => $userMail->verify), function($message) use ($userMail)
      {
        $message->to($userMail->email)->subject('You\'re Almost Done!');
      });

      $this->log->addInfo("SendVerification", array(
        "steamUserId" => Session::get("user.id"),
        "displayName" => Session::get("user.name"),
        "ipAddress" => Request::getClientIp()
      ));

      Session::put('email.send', time());
    }

    return Redirect::route('subscribe');
  }

  public function verifyEmail($verificationCode = null)
  {

    if($verificationCode == null) return Redirect::route('home');

    $find = mailList::whereVerify($verificationCode)->first();

    if($find != null) {

      $this->log->addInfo("Finish Verification", array(
        "steamUserId" => $find->steam_user_id,
        "displayName" => Session::get("user.name"),
        "ipAddress" => Request::getClientIp()
      ));

      $find->verify = 'done';
      $find->save();
    }
    if(Session::get('user.in')) {
      return Redirect::route('subscribe');
    }

    return Redirect::route('home');

  }

  public function getASubscribedUser()
  {
    if(Cache::has('getLastCheckedUser')) {
      $getLastCheckedUser = Cache::get('getLastCheckedUser');
      $getNewUser = mailList::whereRaw('id > ? and verify = ?', array($getLastCheckedUser, 'done'))->first();

      if(!is_object($getNewUser)) {
        Cache::forget('getLastCheckedUser');
        Cache::forever('getLastCheckedUser', -1);
        return $this->getASubscribedUser();
      } else {
        Cache::forget('getLastCheckedUser');
        Cache::forever('getLastCheckedUser', $getNewUser->id);
      }

      return $getNewUser;
    }
    Cache::forever('getLastCheckedUser', -1);
    return $this->getASubscribedUser();
  }

  public function checkUserList($vBanList, $steamUserId)
  {
    $suspects = array();
    foreach($vBanList->all() as $vBanUser) {
      if($vBanUser->check_banned < $vBanUser->vBanUser->num_of_bans)
        $suspects[] = $vBanUser;
    }

    $bannedUsers = array();

    foreach($suspects as $suspect) {

      $getBanInfo = $this->cURLPage( "http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key={$this->steamAPI}&steamids={$suspect->vBanUser->community_id}&".time() ) or
      $this->log->addError("fileLoad", array(
        "steamId" => Session::get('user.id'),
        "displayName" => Session::get('user.name'),
        "ipAddress" => Request::getClientIp(),
        "controller" => "checkUserList@MailController"
      ));
      $getBanInfo = $getBanInfo->players[0];

      if(!is_object($getBanInfo))
      {
        $this->log->addWarning("unknownContent", array(
          "steamId" => Session::get('user.id'),
          "displayName" => Session::get('user.name'),
          "ipAddress" => Request::getClientIp(),
          "controller" => "checkUserList@MailController"
        ));
        return false;
      }
      $num_of_bans = $getBanInfo->NumberOfVACBans;

      if($num_of_bans > $suspect->check_banned) {
        $this->updateVBanUser(null, $suspect->vBanUser->community_id);
        $suspect->check_banned = $num_of_bans;
        $suspect->save();
        $bannedUsers[] = $suspect;
      }
    }
    if(count($bannedUsers) > 0) {
      $userMail = mailList::whereSteamUserId($steamUserId)->first();

      Mail::send(
        'emails.foundBannedUser',
        Array('bannedUsers' => $bannedUsers),
        function($message) use ($userMail)
        {
          $message->to($userMail->email)->subject('Hackers Found On VacStatus!');
        }
      );

      return true;
    } else {
      return false;
    }
  }
}
?>
