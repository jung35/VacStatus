<?php
class AdminController extends BaseController {

  public function getIndex()
  {
    // CACHE TIME

    if(!Cache::has('admin-steamUsers')) {
      Cache::add('admin-steamUsers', steamUser::all()->count(), 60);
    }

    if(!Cache::has('admin-subbedUsers')) {
      Cache::add('admin-subbedUsers', mailList::whereVerify('done')->count()." / ".mailList::all()->count(), 60);
    }

    if(!Cache::has('admin-recordedUsers')) {
      Cache::add('admin-recordedUsers', vBanUser::all()->count(), 60);
    }

    if(!Cache::has('admin-listedUsers')) {
      Cache::add('admin-listedUsers', vBanList::all()->count(), 60);
    }

    if(!Cache::has('admin-avgListedUsers')) {
      $sum = 0;
      $i = 0;
      foreach(steamUser::all() as $steamUser) {
        if($steamUser->vBanList->count() > 0) {
          $sum += $steamUser->vBanList->count();
          $i++;
        }
      }
      Cache::add('admin-avgListedUsers', number_format((float)@($sum / $i), 2, '.', ''), 1440);
    }

    if(!Cache::has('admin-news')) {
      Cache::add('admin-news', DB::table('siteNews')->count(), 60);
    }

    $stats = array();
    $stats['steamUsers'] = Cache::get('admin-steamUsers');
    $stats['subbedUsers'] = Cache::get('admin-subbedUsers');
    $stats['recordedUsers'] = Cache::get('admin-recordedUsers');
    $stats['listedUsers'] = Cache::get('admin-listedUsers');

    $stats['avgListedUsers'] = Cache::get('admin-avgListedUsers');
    $stats['news'] = Cache::get('admin-news');

    $logDir = __dir__.'/../storage/logs/';
    $opendir = array_slice(array_diff(scandir($logDir, 1), array('..', '.')), 0, 5);

    foreach($opendir as $file) {
      $logs[] = Array($file, number_format((float)(filesize($logDir.$file) / 1024), 2, '.', ''));
    }

    return View::make('admin.index', array('stats' => $stats, 'logs' => $logs));
  }

  public function getLog($fileName)
  {

    $logDir = __dir__.'/../storage/logs/';
    if(!file_exists($logDir.$fileName)) {
      return Redirect::route('admin.index');
    }

    $info['name'] = $fileName;
    $info['size'] = number_format((float)(filesize($logDir.$fileName) / 1024), 2, '.', '');

    $file = file($logDir.$fileName);

    $parsedFile = array();

    foreach($file as $line) {
      preg_match('/(\[.+\]) production\.(\S+)\: (\S+) (\{.*\})/', $line, $matched);
      $parsedFile[] = array_slice($matched, 1, 5);
    }

    return View::make('admin.logView', array('info' => $info, 'log' => array_filter(array_reverse($parsedFile))));
  }

  public function getNews()
  {
    $siteNewses = DB::table('siteNews')->orderBy('id', 'desc')->paginate(10);

    return View::make('admin.newsView', array('siteNewses' => $siteNewses));
  }

  public function postNewNews()
  {
    $newsTitle = Input::get('form-title');
    $newsNews = Input::get('form-news');

    DB::table('siteNews')->insert(
        array('title' => $newsTitle,
              'news' => $newsNews)
    );

    return Redirect::route('admin.news');
  }

  public function postDelNews()
  {
    $newsId = Input::get('form-id');

    DB::table('siteNews')->where('id', $newsId)->delete();

    return Redirect::route('admin.news');
  }

  public function getEditNews($newsId)
  {
    $siteNews = DB::table('siteNews')->where('id', $newsId)->first();

    if(!is_object($siteNews)) {
      return Redirect::route('admin.news');
    }

    return View::make('admin.newsEdit', array('siteNews' => $siteNews));
  }

  public function postEditNews()
  {
    $newsId = Input::get('form-id');
    $newsTitle = Input::get('form-title');
    $newsNews = Input::get('form-news');

    DB::table('siteNews')->where('id', $newsId)->update(array(
      'title' => $newsTitle,
      'news' => $newsNews
    ));

    return Redirect::route('admin.news');
  }
}
