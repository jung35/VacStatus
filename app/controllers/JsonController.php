<?php
class JsonController extends BaseController {

  public function getIndex() {

  }

  public function postUser()
  {
    if(Request::wantsJson()) {
      $steamCommunityId = bcadd(Input::get('communityId'), '76561197960265728');
      $dated = Input::get('dated');
      $vBanUser = $this->updateVBanUser(null, $steamCommunityId);

      if(!is_object($vBanUser)) {
        $vBanUser = array('status' => 'error');
      } else {
        unset($vBanUser->user_alias);
        $userInfo = Array(
          'html' => View::make('user.userSlide', array('vBanUser' => $vBanUser, 'displayAdded' => $dated))->render(),
          'status' => 'success');
      }
      return Response::json($userInfo);
    }
    return 'Please request Json';
  }
}
