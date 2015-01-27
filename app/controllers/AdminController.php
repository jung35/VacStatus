<?php

class AdminController extends \BaseController {

    public function indexAction()
    {
        //These times are in minutes!
        if(!Cache::has('admin-profiles') || !Cache::has('admin-users') || !Cache::has('admin-list-total'))
        {
            Cache::add('admin-profiles', Profile::all()->count(), 10);
            Cache::add('admin-users', User::all()->count(), 10);
            Cache::add('admin-list-total', UserList::all()->count(), 10);
        }



        if(!Cache::has('admin-list-public') || !Cache::has('admin-list-friend') || !Cache::has('admin-list-private'))
        {
          Cache::add('admin-list-public', UserList::wherePrivacy(1)->count(), 30);
          Cache::add('admin-list-friend', UserList::wherePrivacy(2)->count(), 30);
          Cache::add('admin-list-private', UserList::wherePrivacy(3)->count(), 30);
        }



        if(!Cache::has('admin-donation') || !Cache::has('admin-donation-after') || !Cache::has('admin-donation-average'))
        {

            $donations = DonationLog::whereStatus('Completed');
            $totalOriginalAmount = 0;
            $totalAmountAfter = 0;
            $i = 0;

            foreach($donations->get() as $donation) {
                $totalOriginalAmount += $donation->getOriginalAmount();
                $totalAmountAfter += $donation->getAmount();
                $i++;
            }

            $average = $i ? $totalOriginalAmount / $i : 0;

            $totalOriginalAmount = number_format($totalOriginalAmount, 2, '.', '');
            $totalAmountAfter = number_format($totalAmountAfter, 2, '.', '');
            $average = number_format($average, 2, '.', '');

            Cache::add('admin-donation', '$' . $totalOriginalAmount, 30);
            Cache::add('admin-donation-after', '$' . $totalAmountAfter, 30);
            Cache::add('admin-donation-average', '$' . $average, 30);
        }

        $logs = array();
        $logDir = __dir__.'/../storage/logs/';
        $opendir = array_slice(array_diff(scandir($logDir, 1), array('..', '.')), 0, 5);

        foreach($opendir as $file) {
            $logs[] = Array($file, number_format((float)(filesize($logDir.$file) / 1024), 2, '.', ''));
        }

        $siteInfo = Array(
            'admin-profiles'         => Cache::get('admin-profiles'),
            'admin-users'            => Cache::get('admin-users'),
            'admin-list-total'       => Cache::get('admin-list-total'),

            'admin-list-public'      => Cache::get('admin-list-public'),
            'admin-list-friend'      => Cache::get('admin-list-friend'),
            'admin-list-private'     => Cache::get('admin-list-private'),

            'admin-donation'         => Cache::get('admin-donation'),
            'admin-donation-after'   => Cache::get('admin-donation-after'),
            'admin-donation-average' => Cache::get('admin-donation-average'),
        );

        return View::make('admin/index', array(
            'siteInfo' => $siteInfo,
            'logs' => $logs
        ));
    }

    public function newsAction()
    {
        $news = News::orderBy('id', 'desc')->paginate(10);

        return View::make('admin/news', array(
            'news' => $news
        ));
    }

    public function newsCreateAction()
    {
        $newsTitle = Input::get('news_title');
        $newsBody = Input::get('news_body');

        $news = new News;
        $news->title = $newsTitle;
        $news->body = $newsBody;

        if(!$news->save()) {
            return Redirect::back()->with('error', 'Sorry, error saving to database.');
        }

        return Redirect::route('admin_news')->with('success', 'Successfully created new article.');
    }

    public function newsEditAction($newsId)
    {

        if(!is_numeric($newsId)) {
            return Redirect::route('admin_news')->with('error', 'Invalid ID.');
        }

        $news = News::whereId($newsId)->first();

        if(!is_object($news)) {
            return Redirect::route('admin_news')->with('error', 'Could not find article.');
        }

        return View::make('admin/newsEdit', array('news' => $news));
    }

    public function newsPostEditAction()
    {
        $newsId = Input::get('news_id');
        $newsTitle = Input::get('news_title');
        $newsBody = Input::get('news_body');

        $news = News::whereId($newsId)->first();
        $news->title = $newsTitle;
        $news->body = $newsBody;
        $news->save();

        return Redirect::route('admin_news')->with('success', 'Successfully edited the article.');
    }

    public function newsDeleteAction()
    {
        $newsId = Input::get('news_id');
        News::whereId($newsId)->delete();
        return Redirect::route('admin_news')->with('success', 'Successfully removed an article.');
    }
}
