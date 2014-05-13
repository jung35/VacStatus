<?php
class AdminController extends BaseController {

  public function __construct() {
    parent::__construct();
    if(Session::get('user.admin') <= 0) return View::make('noAdmin');
  }

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
      Cache::add('admin-avgListedUsers', number_format((float)($sum / $i), 2, '.', ''), 1440);
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

  public function getLog($fileName) {

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

}
