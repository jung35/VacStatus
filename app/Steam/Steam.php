<?php
namespace Steam;

use \Cache as Cache;
/**
 * Steam Class
 * Handles all of interactions with STEAM API except for login
 */
Class Steam {
  /**
   * Time in seconds before profile is called to an update
   * @var Integer
   */
  public static $UPDATE_TIME = 0; // 1 HOUR = 3600

  public static $LIST_LIMIT = 5;

  public static function getAPI() {
    return $_ENV['STEAM_API'];
  }

  /**
   * Check to see if the profile's last update was long enough for new update
   * @param  Integer $updated_at last time updated
   *
   * @return Boolean
   */
  public static function canUpdate($smallId) {
    if(Cache::has("profile_$smallId")) {
      if(Cache::get("profile_$smallId") + self::$UPDATE_TIME > time()) {
        return false;
      }
    }
    return true;
  }

  /**
   * Instead of using db to see when the profile has been last updated, use cache
   * @param Integer $smallId
   *
   * @return Void
   */
  public static function setUpdate($smallId) {
    Cache::put("profile_$smallId", time(), self::$UPDATE_TIME / 60);
    return;
  }

  /**
   * Conversion of Steam3 ID to smaller number to work easier with
   * @param Integer $steam3Id
   *
   * @return Integer/Array
   */
  public static function toSmallId($steam3Id = null)
  {
    if(is_array($steam3Id)) {
      $smallIds = Array();
      foreach($steam3Id as $key => $value) {
          $smallIds[$key] = explode('.', bcsub($value,'76561197960265728'))[0];
      }
      return $smallIds;
    }
    if($steam3Id && is_numeric($steam3Id)) {
      $steam3Id .= '';
      return explode('.', bcsub($steam3Id,'76561197960265728'))[0];
    }

    return Array('type' => 'error',
                 'data' => 'nan');
  }

  /**
   * Conversion of smaller steam3 ID to its regular number to work easier with
   * @param Integer $smallId
   *
   * @return Integer/Array
   */
  public static function toBigId($smallId = null)
  {
    if($smallId && is_numeric($smallId)) {
      $smallId .= '';
      return explode('.', bcadd($smallId,'76561197960265728'))[0];
    }

    return Array('type' => 'error',
                 'data' => 'nan');
  }

  /**
   * Converts from Steam3 ID to Steam2 ID
   * @param  Integer $steam3Id
   *
   * @return String/Array
   */
  public static function toSteam2Id($steam3Id = null)
  {
    if($steam3Id && is_numeric($steam3Id)) {
      $steamIdPartOne = (substr($steam3Id,-1)%2 == 0) ? 0 : 1;
      $steamIdPartTwo = bcsub($steam3Id,'76561197960265728');
      if (bccomp($steamIdPartTwo,'0') == 1) {
        $steamIdPartTwo = bcsub($steamIdPartTwo, $steamIdPartOne);
        $steamIdPartTwo = bcdiv($steamIdPartTwo, 2);
        return "STEAM_0:$steamIdPartOne:".explode('.', $steamIdPartTwo)[0];
      }
    }

    return Array('type' => 'error',
                 'data' => 'nan');
  }

  /**
   * Using cURL to request to Steam API Servers
   * @param  String $type ('info', 'friends', 'ban', 'alias', 'xmlInfo')
   * @param  String/Array $value
   *
   * @return Object
   */
  public static function cURLSteamAPI($type = null, $value = null, $try = true) {

    // Maybe it should have default type...?
    if($type == null || $value == null) return false;

    $steamAPI = self::getAPI();

    // So this url doesn't float in some files as many different url's
    // keeping them in one place
    switch($type) {
      // Get most of all public information about this steam user
      case 'info':
        if(is_array($value)) {
          $value = implode(',', $value);
        }
        $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$steamAPI}&steamids={$value}&".time();
        break;

      // Get list of friends (Profile must not be private)
      case 'friends':
        if(is_array($value)) {
          $value = $value[0];
        }
        $url = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key={$steamAPI}&steamid={$value}&relationship=friend&".time();
        break;

      // Get more detailed information about this person's ban status
      case 'ban':
        if(is_array($value)) {
          $value = implode(',', $value);
        }
        $url = "http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key={$steamAPI}&steamids={$value}&".time();
        break;

      // Get list of usernames this user has used
      case 'alias':
        if(is_array($value)) {
          $value = $value[0];
        }
        $url = "http://steamcommunity.com/profiles/{$value}/ajaxaliases?".time();
        break;

      // For checking to make sure a user exists by this profile name
      case 'vanityUrl':
        if(is_array($value)) {
          $value = $value[0];
        }
        $url = "http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key={$steamAPI}&vanityurl={$value}&".time();
        break;
    }


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 7);

    try {
      $data = curl_exec($ch);
    } catch(Exception $e) {
      if($try) {
        return self::cURLSteamAPI($type, $value, false);
      }
      return (object) array('type' => 'error',
                            'data' => 'api_conn_err');
    }
    curl_close($ch);

    $data = json_decode($data);
    if(!is_object($data) && !is_array($data)) {
      return (object) array('type' => 'error',
                            'data' => 'api_data_err');
    }
    return $data;
  }

}
