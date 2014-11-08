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
      Finished grabbing things from API
       */

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

      $httpsURL = 'https://store.steampowered.com/avatar/';
      $httpURL = 'http://media.steampowered.com/steamcommunity/public/images/avatars/';

      $profile->avatar_thumb = str_replace($httpURL, $httpsURL, $steamAPI_Info->avatar);
      $profile->avatar = str_replace($httpURL, $httpsURL, $steamAPI_Info->avatarfull);

      $profile->alias = json_encode($steamAPI_alias);

      $profile->save();

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
      Call profile again because I'm currently too tired to think of
      a good way to join the users table together.
       */

      $profile = Profile::where('profile.small_id',Steam::toSmallId($steam3Id))
        ->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
        ->first([
          'profile.id',
          'profile.small_id',
          'profile.display_name',
          'profile.privacy',
          'profile.avatar_thumb',
          'profile.avatar',
          'profile.profile_created',
          'profile.alias',
          'profile.created_at',
          'profile.updated_at',

          'users.donation',
          'users.site_admin',
          'users.beta',
        ]);

      /*
      get the counts
       */
      $gettingCount = UserListProfile::whereProfileId($profile->id)
        ->orderBy('id','desc')
        ->get();

      $profile->getCount = UserList::getCount($gettingCount);
      $profile->getCount = isset($profile->getCount[$profile->id])? $profile->getCount[$profile->id] : 0;
      $profile->lastCount = isset($gettingCount[0]) ? strtotime($gettingCount[0]->created_at) : 0;

      /*
      Tell cache that steam profile has been updated
       */

      Steam::setUpdate(Steam::toSmallId($steam3Id));

      return $profile;
    }
  }

  /**
   * Almost like the single update, but with ability to update many.
   * @param  Array $steam3Ids Array of steam 3 ids
   * @return void
   */
  public static function updateMulitipleProfile($steam3Ids) {
    if($steam3Ids && is_array($steam3Ids)) {

      /*
      Some kids like to smash their forehead on keyboard and make same requests more than once
       */
      $steam3Ids = array_unique($steam3Ids);

      $originalSteam3Ids = $steam3Ids;

      // Grab user info using steam API and since this is only updating single user, just get to the key -> 0
      $steamAPI_Info = Steam::cURLSteamAPI('info', $steam3Ids);
      // Steam web api sux m8
      if(isset($steamAPI_Info->type) && $steamAPI_Info->type == 'error') {
        return $steamAPI_Info->type;
      }
      $steamAPI_Info = $steamAPI_Info->response->players; // multiple profiles

      /*
      Grab information about ban
       */

      // Grab user detailed ban info
      $steamAPI_Bans = Steam::cURLSteamAPI('ban', $steam3Ids);
      if(isset($steamAPI_Bans->type) && $steamAPI_Bans->type == 'error') {
        return $steamAPI_Bans->type;
      }

      $steamAPI_Bans = $steamAPI_Bans->players;

      // Grabbed everything from api except alias

      $arrBySmallId = array();

      for($i = 0; $i < count($steamAPI_Info); $i++) {
        $steamAPI_Info_user = $steamAPI_Info[$i];
        $steamAPI_Ban = $steamAPI_Bans[$i];

        // Grab user's recent aliases that were used. Then sort them by time because steam gives weird timestamp
        $steamAPI_alias = Steam::cURLSteamAPI('alias', $steamAPI_Info_user->steamid);
        if(isset($steamAPI_alias->type) && $steamAPI_alias->type == 'error') {
          break;
        }
        usort($steamAPI_alias, array('Steam\SteamUser', 'aliasSort'));

        // Save them to an array based on smallid -> (converted from steam3id)

        if(!isset($arrBySmallId[Steam::toSmallId($steamAPI_Info_user->steamid)])) {
          $arrBySmallId[Steam::toSmallId($steamAPI_Info_user->steamid)] = array();
        }

        $arrBySmallId[Steam::toSmallId($steamAPI_Info_user->steamid)]['user'] = $steamAPI_Info_user;
        $arrBySmallId[Steam::toSmallId($steamAPI_Info_user->steamid)]['alias'] = $steamAPI_alias;

        if(!isset($arrBySmallId[Steam::toSmallId($steamAPI_Ban->SteamId)])) {
          $arrBySmallId[Steam::toSmallId($steamAPI_Ban->SteamId)] = array();
        }
        $arrBySmallId[Steam::toSmallId($steamAPI_Ban->SteamId)]['ban'] = $steamAPI_Ban;
      }

      $profiles = (object) self::whereIn('profile.small_id', Steam::toSmallId($steam3Ids))
        ->join('profile_ban', 'profile.id', '=', 'profile_ban.profile_id')
        ->leftjoin('users', 'profile.small_id', '=', 'users.small_id')
        ->get([
          'profile.id',
          'profile.small_id',
          'profile.display_name',
          'profile.privacy',
          'profile.avatar_thumb',
          'profile.avatar',
          'profile.profile_created',
          'profile.alias',
          'profile.created_at',
          'profile.updated_at',

          'profile_ban.community',
          'profile_ban.vac',
          'profile_ban.vac_days',
          'profile_ban.trade',
          'profile_ban.unban',

          'users.donation',
          'users.site_admin',
          'users.beta',
        ]);

      /*
      We want to include the # of people tracking and it's not on the db.
      Can't just take it out like magic. So, insert the new and updated array of profile
      into here while under forloop
       */
      $newProfile = array();

      /*
      Start updating the user profile with new data from Steam Web API
       */
      foreach($profiles as $profile) {
        if(!isset($arrBySmallId[$profile->small_id]) ||
           !is_object($profile->small_id) ||
           !is_array($profile->small_id)) {
          continue;
        }
        /*
        Make note that the following profile was found under steamid
         */
        unset($steam3Ids[array_search(Steam::toBigId($profile->small_id), $steam3Ids)]);

        $profile_Info = $arrBySmallId[$profile->small_id]['user'];
        $profile_Alias = $arrBySmallId[$profile->small_id]['alias'];
        $profile_Ban = $arrBySmallId[$profile->small_id]['ban'];

        // Assuming profile exists because it wouldnt go here if it doesnt

        // Make sure to update if this was private and now suddenly public
        if(empty($profile->profile_created) && isset($profile_Info->timecreated)) {
          $profile->profile_created = $profile_Info->timecreated;
        }

        $profile->display_name = $profile_Info->personaname;
        $profile->privacy = $profile_Info->communityvisibilitystate;

        $httpsURL = 'https://store.steampowered.com/avatar/';
        $httpURL = 'http://media.steampowered.com/steamcommunity/public/images/avatars/';

        $profile->avatar_thumb = str_replace($httpURL, $httpsURL, $profile_Info->avatar);
        $profile->avatar = str_replace($httpURL, $httpsURL, $profile_Info->avatarfull);

        $profile->alias = json_encode($profile_Alias);

        $profile->save();

        /*
        Start updating Ban / Create if not already exist under user
         */

        $profileBan = $profile->ProfileBan;

        if(!isset($profileBan->id)) {
          $profileBan = new ProfileBan;
          $profileBan->profile_id = $profile->id;
          $profileBan->unban = false;
        } else {
          if($profileBan->vac > $profile_Ban->NumberOfVACBans) {
            $profileBan->unban = true;
          }
        }

        $profileBan->vac = $profile_Ban->NumberOfVACBans;
        $profileBan->community = $profile_Ban->CommunityBanned;
        $profileBan->trade = $profile_Ban->EconomyBan != 'none';
        $profileBan->vac_days = $profile_Ban->DaysSinceLastBan;

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
        get the counts
         */
        $gettingCount = UserListProfile::whereProfileId($profile->id)
          ->orderBy('id','desc')
          ->get();

        $profile->getCount = UserList::getCount($gettingCount);
        $profile->get_num_tracking = isset($profile->getCount[$profile->id])? $profile->getCount[$profile->id] : 0;
        $profile->lastCount = isset($gettingCount[0]) ? strtotime($gettingCount[0]->created_at) : 0;

        /*
        Tell cache that steam profile has been updated
         */
        Steam::setUpdate($profile->small_id);

        /*
        Add profile to new profiles variable so the # of tracking can be used
         */
        $newProfile[] = $profile;
      }

      /*
      Creating new profiles because they dont exist in the database
       */

      foreach($steam3Ids as $newSteamId) {
        $smallId = Steam::toSmallId($newSteamId);

        $profile_Info = $arrBySmallId[$smallId]['user'];
        $profile_Alias = $arrBySmallId[$smallId]['alias'];
        $profile_Ban = $arrBySmallId[$smallId]['ban'];

        $profile = new self;
        $profile->small_id = Steam::toSmallId($profile_Info->steamid);

        /*
        Sometimes people like to have their profiles private.
        Why? I have no clue
         */
        if(isset($profile_Info->timecreated)) {
          $profile->profile_created = $profile_Info->timecreated;
        }

        $profile->display_name = $profile_Info->personaname;
        $profile->privacy = $profile_Info->communityvisibilitystate;

        $httpsURL = 'https://store.steampowered.com/avatar/';
        $httpURL = 'http://media.steampowered.com/steamcommunity/public/images/avatars/';

        $profile->avatar_thumb = str_replace($httpURL, $httpsURL, $profile_Info->avatar);
        $profile->avatar = str_replace($httpURL, $httpsURL, $profile_Info->avatarfull);

        $profile->alias = json_encode($profile_Alias);

        $profile->save();

        /*
        Start updating Ban / Create if not already exist under user
         */

        $profileBan = $profile->ProfileBan;

        if(!isset($profileBan->id)) {
          $profileBan = new ProfileBan;
          $profileBan->profile_id = $profile->id;
          $profileBan->unban = false;
        } else {
          if($profileBan->vac > $profile_Ban->NumberOfVACBans) {
            $profileBan->unban = true;
          }
        }

        $profileBan->vac = $profile_Ban->NumberOfVACBans;
        $profileBan->community = $profile_Ban->CommunityBanned;
        $profileBan->trade = $profile_Ban->EconomyBan != 'none';
        $profileBan->vac_days = $profile_Ban->DaysSinceLastBan;

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
        get the counts
         */
        $gettingCount = UserListProfile::whereProfileId($profile->id)
          ->orderBy('id','desc')
          ->get();

        $profile->getCount = UserList::getCount($gettingCount);
        $profile->get_num_tracking = isset($profile->getCount[$profile->id])? $profile->getCount[$profile->id] : 0;
        $profile->lastCount = isset($gettingCount[0]) ? strtotime($gettingCount[0]->created_at) : 0;

        /*
        Tell cache that steam profile has been updated
         */

        Steam::setUpdate($profile->small_id);

        /*
        Add profile to new profiles variable so the # of tracking can be used
         */
        $newProfile[] = $profile;
      }

      return (object)$newProfile;
    }
    return false;
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
