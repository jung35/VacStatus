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
}
