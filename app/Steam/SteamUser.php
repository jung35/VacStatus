<?php

namespace Steam;

Class SteamUser extends Steam {

  /**
   * Because steam alias isnt given in order, I have to sort it myself using usort.
   * @param  alias1 $a
   * @param  alias2 $b
   * @return Integer
   */
  public static function aliasSort($a, $b) {
    return strcmp(self::aliasTimeConvert($b->timechanged), self::aliasTimeConvert($a->timechanged));
  }

  /**
   * Steam gives weird timestamp of when the alias was used.
   * @param  String $time
   * @return Integer
   */
  public static function aliasTimeConvert($time) {
    return strtotime(str_replace("@", "", $time));
  }

  public static function cleanAliasDate($time) {
    return date('M j Y, g:i a', strtotime(str_replace("@", "", $time)));
  }

  public static function findSteam3IdUser($data)
  {
    $data = strtolower(trim($data));
    if (!empty($data))
    {
      if (strlen($data) > 100) return (object) array('type' => 'error', 'data' => 'Field too long');

      if (substr($data, 0, 6) == 'steam_' ||
          substr($data, 0, 2) == 'u:')
      {
        $tmp = explode(':',$data);
        if ((count($tmp) == 3) && is_numeric($tmp[1]) && is_numeric($tmp[2]))
        {
          $steam3Id = bcadd(($tmp[2] * 2) + $tmp[1], '76561197960265728');
          return (object) array('type' => 'success','data' => $steam3Id);
        }
        else
        {
          return (object) array('type' => 'error', 'data' => 'Invalid Steam ID');
        }

      }
      else if ($p = strrpos($data, '/'))
      {
        $tmp = explode('/',$data);
        $a = null;
        foreach ($tmp as $key => $item)
        {
          if (is_numeric($item))
          {
            $a = $item;
            break;
          } else if ($item == 'id') {
            $data = $tmp[$key+1];
          }
        }
        if (is_numeric($a) && preg_match('/7656119/', $a))
        {
          return (object) array('type' => 'success', 'data' => $a);
        }
        else
        {
          $steamAPI_vanityUrl = parent::cURLSteamAPI('vanityUrl', $data);
          if(isset($steamAPI_vanityUrl->type) && $steamAPI_vanityUrl->type == 'error' || !isset($steamAPI_vanityUrl->response->steamid) && $steamAPI_vanityUrl->response->success == 42) {
            return (object) array('type' => 'error',
                                  'data' => 'Invalid input');
          }

          $steamid64 = (string) $steamAPI_vanityUrl->response->steamid;
          if (!preg_match('/7656119/', $steamid64)) return (object) array('type' => 'error', 'data' => 'Invalid link');
          else return (object) array('type' => 'success', 'data' => $steamid64);
        }
      }
      else if (is_numeric($data) && preg_match('/7656119/', $data))
      {
        return (object) array('type' => 'success', 'data' => $data);
      }
      else
      {
        $steamAPI_vanityUrl = parent::cURLSteamAPI('vanityUrl', $data);
        if(isset($steamAPI_vanityUrl->type) && $steamAPI_vanityUrl->type == 'error' || !isset($steamAPI_vanityUrl->response->steamid) && $steamAPI_vanityUrl->response->success == 42) {
          return (object) array('type' => 'error',
                                'data' => 'Invalid input');
        }

        $steamid64 = (string) $steamAPI_vanityUrl->response->steamid;
        if (!preg_match('/7656119/', $steamid64)) return (object) array('type' => 'error', 'data' => 'Invalid input');
        else return (object) array('type' => 'success', 'data' => $steamid64);
      }
    }

    return (object) array('type' => 'error', 'data' => 'Invalid or empty input');
  }
}
