<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

/**
 * Hybrid_Providers_Steam provider adapter based on OpenID protocol
 *
 * http://hybridauth.sourceforge.net/userguide/IDProvider_info_Steam.html
 */
class Hybrid_Providers_Steam extends Hybrid_Provider_Model_OpenID
{
  var $openidIdentifier = "http://steamcommunity.com/openid";

  /**
  * finish login step
  */
  function loginFinish()
  {
    parent::loginFinish();

    $uid = str_replace( "http://steamcommunity.com/openid/id/", "", $this->user->profile->identifier );
    if( $uid ){
      // restore the user profile
      Hybrid_Auth::storage()->set( "hauth_session.{$this->providerId}.user", $this->user );
    }
  }
}
