<?php

use Steam\Steam as Steam;
use Steam\SteamUser as SteamUser;

class HomeController extends BaseController {

    /**
    * Main page display
    * @return View index page
    */
    public function indexAction($uorl = 'most', $list = null)
    {
        /*
        Updates multiple profile with the most efficiently way possible (I hope)

        Use strings because with int, it rounds early and string seems like easy fix.
        */
        if($list == null) {
            $req = $uorl;
        } else {
            $req = array($uorl, $list);
        }

        $userList = UserList::getListType($req);
        $news = DB::table('news')->orderBy('id','desc')->take(10)->get();
        $friendsList = array();

        if(Auth::check() && Cache::has('friendsList_'.$userId) && count(Cache::get('friendsList_'.$userId)) != 0) {
            $userId = Auth::User()->getId();
            $friendsList = User::whereIn('small_id', Cache::get('friendsList_'.$userId))->get();
        }

        $viewArray = array(
            'userList'    => $userList,
            'friendsList' => $friendsList,
            'news'        => $news
        );

        if(Auth::check() && isset($userList->custom)) {
            $viewArray['userMail'] = Auth::User()->UserMail;
            $viewArray['subscription'] = Subscription::whereUserListId($userList->list_id)->first();
        }

        return View::make('main/index', $viewArray);
    }

    public function searchSingleAction()
    {
        $search = Input::get('search');
        $steam3Id = SteamUser::findSteam3IdUser($search);

        if($steam3Id->type == 'error') {
            return Redirect::home()->with('error', 'Could not find a user based on given information.');
        }

        return Redirect::route('profile', array('steam3Id' => $steam3Id->data));
    }

    public function searchMultipleAction()
    {
        $search = Input::get('search');

        if(!isset($search)) {
            return Redirect::home()->with('error', 'Invalid fields.');
        }

        $statusChecker = array_filter(explode("\n", $search));
        $statusConfirm = false;
        $searchArray = array();

        foreach($statusChecker as $status) {
            if(substr(trim($status), 0, 1) == "#")
            {
                preg_match("(STEAM_.*?\s)", trim($status), $foundSteam);
                if(count($foundSteam) == 0) continue;
                $searchArray[] = $foundSteam[0];
                $statusConfirm = true;
            }
        }

        if(!$statusConfirm) {
            $search = array_filter(preg_split("/[\s\n]+/", $search));
        } else {
            $search = array_filter($searchArray);
        }

        if(Auth::check())
        {
            if(count($search) > Auth::User()->unlockSearch()) {
                return Redirect::home()->with('error', 'Too many profiles listed in search box.');
            }
        } else if(count($search) > 30) {
            return Redirect::home()->with('error', 'Too many profiles listed in search box.');
        }

        if(!is_array($search))
        {
            return Redirect::home()->with('error', 'Invalid Search Option');
        }

        $validProfile = Array();
        $invalidProfile = Array();

        foreach($search as $potentialProfile)
        {
            $steam3Id = SteamUser::findSteam3IdUser($potentialProfile);

            if($steam3Id->type == 'error') {
                $invalidProfile[] = $potentialProfile;
            } else {
                $validProfile[] = $steam3Id->data;
            }
        }

        $userList = Profile::updateMulitipleProfile($validProfile);

        if(!is_object($userList)) {
            return Redirect::home()->with('error', 'None of the users exist');
        }

        $userList->title = "Search";

        return View::make('main/search', array('userList' => $userList, 'invalidProfile' => implode(", ", $invalidProfile)));
    }

    public function newsAction($newsId)
    {
        $news = News::whereId($newsId)->first();

        if(!is_object($news)) {
            return Redirect::home()->with('error', 'Could not find news.');
        }

        return View::make('main/news', array('news' => $news));
    }
}
