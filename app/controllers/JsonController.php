<?php
class JsonController extends BaseController {

  public function getIndex() {

  }

  public function postUser()
  {
    $steamCommunityId = Input::get('communityId');
    $vBanUser = vBanUser::wherecommunityId($steamCommunityId)->first();
    $userInfo = $this->updateVBanUser($vBanUser, $steamCommunityId);
    unset($userInfo->user_alias);

    return Response::json($userInfo);
  }
}
