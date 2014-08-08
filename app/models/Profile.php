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

  /**
   * Connect to the old aliases that this user was seen with
   */
  public function ProfileOldAlias() {
    return $this->hasMany('ProfileOldAlias');
  }

  /**
   * Connect to the bans that this user was seen with
   */
  public function ProfileBan() {
    return $this->hasOne('ProfileBan');
  }

  /**
   * Updates single profile in mysql DB
   * It does not check if the profile should be allowed to update or not
   * @param  Integer $steam3Id
   *
   * @return Object
   */
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

      /*
      Start updating the user profile with new data from Steam Web API
       */

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

      /*
      Grab information about ban
       */

      // Grab user detailed ban info
      $steamAPI_Ban = Steam::cURLSteamAPI('ban', $steam3Id);
      if(isset($steamAPI_Ban->type) && $steamAPI_Ban->type == 'error') {
        return $steamAPI_Ban->type;
      }

      $steamAPI_Ban = $steamAPI_Ban->players[0];

      /*
      Start updating Ban / Create if not already exist under user
       */

      $profileBan = $profile->ProfileBan;

      if(!isset($profileBan->id)) {
        $profileBan = new ProfileBan;
        $profileBan->profile_id = $profile->id;
        $profileBan->unban = false;
      } else {
        if($profileBan->vac > $steamAPI_Ban->NumberOfVACBans) {
          $profileBan->unban = true;
        }
      }

      $profileBan->vac = $steamAPI_Ban->NumberOfVACBans;
      $profileBan->community = $steamAPI_Ban->CommunityBanned;
      $profileBan->trade = $steamAPI_Ban->EconomyBan != 'none';
      $profileBan->vac_days = $steamAPI_Ban->DaysSinceLastBan;

      $profile->ProfileBan()->save($profileBan);
      $profile->ProfileBan = $profileBan;

      /*
      Grab & Update Old Alias
       */
      $profileOldAlias = $profile->ProfileOldAlias()->where('profile_id', '=', $profile->id)->get();
      $profileOldAlias = $profileOldAlias->count() ? $profileOldAlias : new ProfileOldAlias;

      if($profileOldAlias->count() == 0) {
        $profileOldAlias->addAlias($profile);
      } else {
        $match = false;
        $recent = 0;
        foreach($profileOldAlias as $oldAlias) {
          if(is_object($oldAlias)) {
            if($oldAlias->getAlias() == $profile->getDisplayName()) {
              $match = true;
              break;
            }
            $recent = $oldAlias->compareTime($recent);
          }
        }

        if(!$match && $recent + Steam::$UPDATE_TIME < time()) {
          $newAlias = new ProfileOldAlias;
          $newAlias->profile_id = $profile->getId();
          $newAlias->seen = time();
          $newAlias->seen_alias = $profile->getDisplayName();
          $profile->ProfileOldAlias()->save($newAlias);
        }
      }

      /*
      Tell cache that steam profile has been updated
       */

      Steam::setUpdate(Steam::toSmallId($steam3Id));

      return $profile;
    }
  }

  public function updateMulitipleProfile($steam3Ids) {
    if($steam3Ids && is_array($steam3Ids)) {
      // Grab user info using steam API and since this is only updating single user, just get to the key -> 0
      $steamAPI_Info = Steam::cURLSteamAPI('info', $steam3Ids);
      // Steam web api sux m8
      if(isset($steamAPI_Info->type) && $steamAPI_Info->type == 'error') {
        return $steamAPI_Info->type;
      }

      $steamAPI_Info = $steamAPI_Info->response->players; // multiple profiles

      foreach($steamAPI_Info as $steamAPI_Info_user) { // sort multiple profiles at once (should I allow this)
        // Grab user's recent aliases that were used. Then sort them by time because steam gives weird timestamp
        $steamAPI_alias = Steam::cURLSteamAPI('alias', $steamAPI_Info_user->steamid);
        if(isset($steamAPI_alias->type) && $steamAPI_alias->type == 'error') {
          break;
        }
        usort($steamAPI_alias, array('Steam\SteamUser', 'aliasSort'));
      }


      $profile = self::whereSmallId(Steam::toSmallId($steam3Id))->first();
    }
  }

  public function getDisplayName() {
    return $this->display_name;
  }

  /**
   * SteamAPI says 3 is public and everything else is private
   *
   * @return boolean
   */
  public function isPrivate() {
    return $this->privacy != 3;
  }

  public function getId() {
    return $this->id;
  }

  public function getSteamCreation() {
    if(isset($this->profile_created)) {
      return date('M j Y', $this->profile_created);
    }
    return "Unknown";
  }

  public function getSmallId() {
    return $this->small_id;
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

  public function getAlias() {
    return json_decode($this->alias);
  }
}
