<?php
class JsonController extends BaseController {

  public function getIndex() {

  }

  public function postUser()
  {
    if(Request::wantsJson()) {
      $steamCommunityId = bcadd(Input::get('communityId'), '76561197960265728');
      $dated = Input::get('dated');
      $searching = Input::get('searching');
      $vBanUser = $this->updateVBanUser(null, $steamCommunityId);

      if(!is_object($vBanUser)) {
        $userInfo = array('status' => 'error');
      } else {
        unset($vBanUser->user_alias);

        if(Session::has('user.in')) {
          $sessionUserId = Session::get('user.in');
          $vBanUser->is_tracking = isset(vBanList::whereRaw( "steam_user_id = {$sessionUserId} and v_ban_user_id = {$vBanUser->id}" )->first()->id)? 1:0;
        }

        $userInfo = Array(
          'html' => View::make('user.userSlide')->with(array('vBanUser' => $vBanUser, 'displayAdded' => $dated, 'searching' => $searching))->render(),
          'status' => 'success');
      }
      return Response::json($userInfo);
    }
    return 'Please request Json';
  }
}
