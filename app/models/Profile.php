<?php

use Steam\Steam as Steam;
use Steam\SteamUser as SteamUser;

class Profile extends \Eloquent {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'profile';

  public function ProfileOldAlias() {
    return $this->hasMany('profile_old_alias');
  }

  public static function updateSingleProfile($steam3Id = null) {

    if($steam3Id) {
      // Grab user info using steam API and since this is only updating single user, just get to the key -> 0
      $steamAPI_Info = Steam::cURLSteamAPI('info', $steam3Id);
      // Steam web api sux m8
      if(isset($steamAPI_Info->type) && $steamAPI_Info->type == 'error') {
        return $steamAPI_Info->type;
      }

      $steamAPI_Info = $steamAPI_Info->response->players[0];

      // Grab user's recent aliases that were used. Then sort them by time because steam gives weird timestamp
      $steamAPI_alias = Steam::cURLSteamAPI('alias', $steam3Id);
      if(isset($steamAPI_alias->type) && $steamAPI_alias->type == 'error') {
        return $steamAPI_alias->type;
      }

      usort($steamAPI_alias, array('Steam\SteamUser', 'aliasSort'));

      $profile = self::whereSmallId(Steam::toSmallId($steam3Id))->first();

      if(!isset($profile->id)) {
        $profile = new self;
        $profile->small_id = Steam::toSmallId($steam3Id);
        if(isset($steamAPI_Info->timecreated)) {
          $profile->profile_created = $steamAPI_Info->timecreated;
        }
      } else {

        // Make sure to update if this was private and now suddenly public
        if(empty($profile->profile_created) && isset($steamAPI_Info->timecreated)) {
          $profile->profile_created = $steamAPI_Info->timecreated;
        }
      }

      $profile->display_name = $steamAPI_Info->personaname;
      $profile->privacy = $steamAPI_Info->communityvisibilitystate;
      $profile->avatar_thumb = $steamAPI_Info->avatar;
      $profile->avatar = $steamAPI_Info->avatarfull;

      $profile->alias = json_encode($steamAPI_alias);

      $profile->save();

      Steam::setUpdate(Steam::toSmallId($steam3Id));

      return $profile;
    }
  }

  public function getDisplayName() {
    return $this->display_name;
  }

  public function isPrivate() {
    return $this->privacy != 3;
  }

  public function getSteamCreation() {
    if(isset($this->profile_created)) {
      return date('M j Y', $this->profile_created);
    }
    return "Unknown";
  }

  public function getSteam3Id() {
    return Steam::toBigId($this->small_id);
  }

  public function getSteam2Id() {
    return Steam::toSteam2Id($this->getSteam3Id($this->small_id));
  }

  public function getAvatar() {
    return $this->avatar;
  }
}
