<?php

class BaseController extends Controller {

  protected $steamAPI;
  protected $log;

  /**
   * Setup the layout used by the controller.
   *
   * @return void
   */
  public function __construct()
  {
    $this->steamAPI = 'FCB152DD0DF8097DD31E21234E07C5C4';

    $this->log = Log::getMonolog();
    DB::connection()->disableQueryLog();

    if(empty($this->steamAPI)) {
      die('STEAMAPI MISSING');
    }
  }

  protected function setupLayout()
  {
    if ( ! is_null($this->layout))
    {
      $this->layout = View::make($this->layout);
    }
  }

  public function grabVBanUser($steamCommunityId, $sessionUserId = null)
  {
    if($sessionUserId == null) $sessionUserId = Session::get('user.id');
    $vBanUser = vBanUser::wherecommunityId($steamCommunityId)->first();

    if(!isset($vBanUser->id) || $vBanUser->vac_banned == 0)
    {
      return false;
    } else {
      $userInfo = $vBanUser;
      $userInfo->steam_id = $this->convertSteamId($userInfo->community_id);
      $userInfo->user_alias = $vBanUser->vBanUserAlias()->orderBy('time_used','desc')->get();
    }

    $userInfo->get_num_tracking = vBanList::wherevBanUserId($userInfo->id)->count();

    if(Session::get('user.in'))
    {
      $userInfo->is_tracking = isset(vBanList::whereRaw( "steam_user_id = {$sessionUserId} and v_ban_user_id = {$userInfo->id}" )->first()->id)? 1:0;
    }

    return $userInfo;
  }

  public function updateVBanUser($vBanUser = null, $steamCommunityId)
  {
    if($vBanUser == null)
    {
      $vBanUser = vBanUser::wherecommunityId($steamCommunityId)->first();
    }
    $userInfo = new stdClass();

    $data = $this->cURLPage( "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->steamAPI}&steamids={$steamCommunityId}&".time());

    if (!is_object($data))
    {
      $this->log->addWarning("unknownContent", array(
        "steamUserId" => Session::get('user.id'),
        "displayName" => Session::get('user.name'),
        "ipAddress" => Request::getClientIp(),
        "controller" => "updateVBanUser@BaseController",
        "line" => __LINE__,
        "data" => $data
      ));
      return false;
    }
    $data = $data->response->players;

    if(isset($data[0])) {
      $data = $data[0];
    } else {
      return false;
    }

    $userInfo->display_name   = (string) $data->personaname;
    $userInfo->community_id   = (string) $data->steamid;
    $userInfo->steam_avatar_url_big      = (string) $data->avatarfull;
    $userInfo->steam_avatar_url_small = (string) $data->avatar;
    if(isset($data->timecreated)) {
      $userInfo->steam_creation = (string) $data->timecreated;
    } else {
      $userInfo->steam_creation = 0;
    }
    $userInfo->private_profile  = (string) $data->communityvisibilitystate == 3? 0:1;

    $userInfo->steam_id = $this->convertSteamId($steamCommunityId);

    $getBanInfo = $this->cURLPage( "http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key={$this->steamAPI}&steamids={$steamCommunityId}&".time());

    if(!is_object($getBanInfo))
    {
      $this->log->addWarning("unknownContent", array(
        "steamUserId" => Session::get('user.id'),
        "displayName" => Session::get('user.name'),
        "ipAddress" => Request::getClientIp(),
        "controller" => "updateVBanUser@BaseController",
        "line" => __LINE__,
        "data" => $data
      ));
      return false;
    }

    $getBanInfo = $getBanInfo->players[0];
    $userInfo->vac_banned = $getBanInfo->VACBanned ? $getBanInfo->DaysSinceLastBan : -1;
    $userInfo->num_of_bans = $getBanInfo->NumberOfVACBans;
    $userInfo->community_banned = $getBanInfo->CommunityBanned == true;
    $userInfo->market_banned = $getBanInfo->EconomyBan == 'banned';

    if(!isset($vBanUser->id)) {
      $vBanUser = new vBanUser;
      $vBanUser->community_id = $steamCommunityId;
    }

    $vBanUser->display_name = $userInfo->display_name;
    $vBanUser->private_profile = $userInfo->private_profile;
    if($vBanUser->steam_creation == 0) {
      $vBanUser->steam_creation = $userInfo->steam_creation;
    } else {
      $userInfo->steam_creation = $vBanUser->steam_creation;
    }
    $vBanUser->steam_avatar_url_big = $userInfo->steam_avatar_url_big;
    $vBanUser->steam_avatar_url_small = $userInfo->steam_avatar_url_small;
    $vBanUser->vac_banned = $userInfo->vac_banned;
    $vBanUser->num_of_bans = $userInfo->num_of_bans;
    $vBanUser->community_banned = $userInfo->community_banned;
    $vBanUser->market_banned = $userInfo->market_banned;
    $vBanUser->save();

    $getUserAlias = $this->cURLPage( "http://steamcommunity.com/profiles/$steamCommunityId/ajaxaliases/?".time());

    if(isset($getUserAlias[0]) && !empty($getUserAlias[0])) {
      vBanUserAlias::where('v_ban_user_id', '=', $vBanUser->id)->delete();
      $userAliasList = array();
      foreach($getUserAlias as $userAlias) {
        $vBanUserAlias = new vBanUserAlias;
        $vBanUserAlias->v_ban_user_id = $vBanUser->id;
        $vBanUserAlias->alias = $userAlias->newname;
        $vBanUserAlias->time_used = explode("@", $userAlias->timechanged);
        $vBanUserAlias->time_used = strtotime($vBanUserAlias->time_used[0]);
        $vBanUserAlias->save();
        $userAliasList[] = $vBanUserAlias;
      }
    } else {
      $userAliasList = $vBanUser->vBanUserAlias()->orderBy('time_used','desc')->get();
    }
    $userInfo->created_at = $vBanUser->created_at;
    $userInfo->updated_at = $vBanUser->updated_at;
    $userInfo->user_alias  = $userAliasList;
    $userInfo->id = $vBanUser->id;

    return $userInfo;
  }

  public function cURLPage($url, $json = true) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    $data = @curl_exec($ch);
    curl_close($ch);

    if($json) {
      $data = json_decode($data);
    }

    return $data;
  }

  public function convertSteamId($steamCommunityId = '')
  {
    $steamIdPartOne = (substr($steamCommunityId,-1)%2 == 0) ? 0 : 1;
    $steamIdPartTwo = bcsub($steamCommunityId,'76561197960265728');
    if (bccomp($steamIdPartTwo,'0') != 1) {
      return "";
    } else {
      $steamIdPartTwo = bcsub($steamIdPartTwo, $steamIdPartOne);
      $steamIdPartTwo = bcdiv($steamIdPartTwo, 2);
      return "STEAM_0:$steamIdPartOne:$steamIdPartTwo";
    }
  }

}
