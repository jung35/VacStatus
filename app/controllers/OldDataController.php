<?php

class OldDataController extends BaseController {

    public function indexAction()
    {
        $steamUser = DB::table('steamUser')->whereCommunityId(Auth::User()->getSteam3Id())->first();

        if(!isset($steamUser->id)) {
            return Redirect::home()->with('error', 'Sorry No trace of your user on old database!');
        }

        $vBanList = DB::table('vBanList')->whereSteamUserId($steamUser->id)->get();
        $steamIds = array();

        foreach($vBanList as $list) {
            $vBanUser = DB::table('vBanUser')->whereId($list->v_ban_user_id)->first();
            $steamIds[] = $vBanUser->community_id;
        }

        return View::make('old/index', array('steamIds' => $steamIds));
    }
}
