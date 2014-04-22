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
if(Session::get('user.in'))
{
  Route::get('', array('as' => 'home', 'uses' => 'AppController@showIndex'));

  Route::post('add', 'AppController@addUser');
  Route::post('remove', 'AppController@removeUser');

  Route::get('add', array('before' => 'csrf', 'as' => 'add', 'uses' => 'AppController@addUser'));
  Route::get('remove', array('before' => 'csrf', 'as' => 'remove', 'uses' => 'AppController@removeUser'));
}
else
{
  Route::get('', array('as' => 'home', 'uses' => 'HomeController@showWelcome'));
}

Route::get('search', array('as' => 'search', 'uses' => 'AppController@doSearch'));
Route::post('search', 'AppController@doSearch');

Route::get('most', array('as' => 'most', 'uses' => 'AppController@listMostUserTracked'));
Route::get('u/{steamCommunityId?}', array('as' => 'user', 'uses' => 'AppController@showUser'));

Route::get('news', array('as' => 'news', 'uses' => 'HomeController@showNews'));
Route::get('about', array('as' => 'about', 'uses' => 'HomeController@showAbout'));

Route::get('login/{action?}', array('as' => 'login', 'uses' => 'HomeController@steamLogin'));
Route::get('logout', array('as' => 'logout', 'uses' => 'HomeController@steamLogout'));

Route::get('privacy', function()
{
    return View::make('privacyPolicy');
});
