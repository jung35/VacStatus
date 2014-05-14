<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::filter('steamAuth', function()
{
    if(!Session::get('user.in')) return View::make('noLogin');
});

if(Session::get('user.in'))
{
  Route::get('', array('as' => 'home', 'uses' => 'AppController@showIndex'));
}
else
{
  Route::get('', array('as' => 'home', 'uses' => 'HomeController@showWelcome'));
}

Route::get('search', array('as' => 'search', 'uses' => 'AppController@doSearch'));
Route::post('search', 'AppController@doSearch');

Route::get('u/{steamCommunityId?}', array('as' => 'user', 'uses' => 'AppController@showUser'));

Route::get('most', array('as' => 'most', 'uses' => 'AppController@listMostUserTracked'));
Route::get('latest', array('as' => 'latest', 'uses' => 'AppController@showLatestUserAdded'));

Route::get('news', array('as' => 'news', 'uses' => 'HomeController@showNews'));
Route::get('about', array('as' => 'about', 'uses' => 'HomeController@showAbout'));

Route::get('login/{action?}', array('as' => 'login', 'uses' => 'HomeController@steamLogin'));
Route::get('logout', array('as' => 'logout', 'uses' => 'HomeController@steamLogout'));

Route::get('verify/{verificationCode}', array('as' => 'verify', 'uses' => 'MailController@verifyEmail'));

Route::get('privacy', function()
{
  return View::make('privacyPolicy');
});

Route::post('add', array('before' => 'steamAuth', 'uses' => 'AppController@addUser'));
Route::post('remove', array('before' => 'steamAuth', 'uses' => 'AppController@removeUser'));

Route::get('add', array('before' => 'steamAuth|csrf', 'as' => 'add', 'uses' => 'AppController@addUser'));
Route::get('remove', array('before' => 'steamAuth|csrf', 'as' => 'remove', 'uses' => 'AppController@removeUser'));

Route::get('subscribe', array('before' => 'steamAuth', 'as' => 'subscribe', 'uses' => 'MailController@showSub'));
Route::post('subscribe', array('before' => 'steamAuth', 'uses' => 'MailController@doSub'));

Route::get('resend', array('before' => 'steamAuth', 'as' => 'resendEmail', 'uses' => 'MailController@sendVerification'));

Route::controller('admin', 'AdminController',
  array(
      'getIndex' => 'admin.index',
      'getLog' => 'admin.log',
      'getNews' => 'admin.news',
      'postNewNews' => 'admin.news.new',
      'postDelNews' => 'admin.news.del',
      'getEditNews' => 'admin.news.edit',
      'postEditNews' => 'admin.news.edit'
  )
);

