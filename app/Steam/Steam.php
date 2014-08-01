<?php

namespace Steam;
/**
 * Steam Class
 *
 * Handles all of interactions with STEAM API except for login
 */
Class Steam {
  /**
   * Valve's Steam Web API. Register for one at http://steamcommunity.com/dev/apikey
   * @var string
   */
  protected static $steam_api = "";
  /**
   * Conversion of Steam3 ID to smaller number to work easier with
   * @param Integer $steam3Id
   *
   * @return Integer/Array
   */
  public static function toSmallId($steam3Id = null)
  {
    if($steam3Id && is_numeric($steam3Id)) {
      return bcsub($steam3Id,'76561197960265728');
    }

    return Array('type' => 'error',
                 'data' => 'Parameter was empty or NaN');
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
        return "STEAM_0:$steamIdPartOne:$steamIdPartTwo";
      }
    }

    return Array('type' => 'error',
                 'data' => 'Parameter was empty or NaN');
  }

  /**
   * Using cURL to request to Steam API Servers
   * @param  String $type
   * @param  String/Array $value
   *
   * @return Object
   */
  public static function cURLSteamAPI($type = null, $value = null) {

    // Maybe it should have default type...?
    if($type == null || $value == null) return false;

    $json = true;

    // So this url doesn't float in some files as many different url's
    // keeping them in one place
    switch($type) {
      // Get most of all public information about this steam user
      case 'info':
        if(is_array($value)) {
          $value = explode(',', $value);
        }
        $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={parent::$steamAPI}&steamids={$value}&".time();
        break;
      // Get more detailed information about this person's ban status

      case 'ban':
        if(is_array($value)) {
          $value = explode(',', $value);
        }
        $url = "http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key={parent::$steamAPI}&steamids={$value}&".time();
        break;

      // Get list of usernames this user has used
      case 'alias':
        $url = "http://steamcommunity.com/profiles/{$value}/ajaxaliases/?".time();
        break;

      // For checking to make sure a user exists by this profile name
      case 'xmlInfo':
        $url = "http://steamcommunity.com/id/{$value}/?xml=1&".time();
        $json = false;
        break;
    }


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    try {
      $data = curl_exec($ch);
    } catch(Exception $e) {
      return (object) array('type' => 'error',
                            'data' => 'Trouble connecting to steam API');
    }
    curl_close($ch);

    if($json) {
      $data = json_decode($data);
      if(!is_object($data)) {
        return (object) array('type' => 'error',
                              'data' => 'Steam API error');
      }
    } else {
      // Still not possible to send request to valve to check by steam profile id via Steam web API :'(
      try {
        $data = simplexml_load_string($data);
      } catch(Exception $e) {
        return (object) array('type' => 'error',
                              'data' => 'Steam API error');
      }
    }


    return $data;

  }

}
