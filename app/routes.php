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

Route::get('/', array('as' => 'home', 'uses' => 'HomeController@indexAction'));
Route::get('/l/{uorl?}/{list?}', array('as' => 'list_display','uses' => 'HomeController@indexAction'));

Route::get('/logout', array('before' => 'auth', 'as' => 'logout', 'uses' => 'LoginController@logoutAction'));

Route::post('/profile_lookup', array('as' => 'search_single', 'uses' => 'HomeController@searchSingleAction'));
Route::post('/search', array('as' => 'search_multi', 'uses' => 'HomeController@searchMultipleAction'));

Route::get('/login/{action?}', array('as' => 'login', 'uses' => 'LoginController@loginAction'));

Route::get('/u/{steam3Id?}', array('as' => 'profile', 'uses' => 'ProfileController@profileAction'));

Route::post('/u/update/single', array('before' => 'csrf', 'uses' => 'ProfileController@updateSingleProfileAction'));

Route::post('/list/fetch', array('before' => 'csrf', 'as' => 'list_fetch', 'uses' => 'DisplayListController@fetchListAction'));
Route::post('/list/update', array('before' => 'csrf', 'as' => 'list_update', 'uses' => 'DisplayListController@updateListAction'));

Route::any('/ipn', array('uses' => 'DonationController@IPNAction'));
Route::any('/donation', array('as' => 'donation', 'uses' => 'DonationController@DonationAction'));

Route::get('/news/{newsId?}', array('as' => 'news', 'uses' => 'HomeController@newsAction'));

Route::group(array('before' => 'auth'), function() {
  Route::resource('subscription', 'SubscriptionController');

  Route::get('/settings', array('as' => 'settings', 'uses' => 'SettingsController@showSettings'));
  Route::get('/settings/verify/{verification}/{type}', array('as' => 'settings_verify', 'uses' => 'SettingsController@verifySettings'));

  Route::group(array('before' => 'csrf'), function() {
    Route::post('/settings', array('as' => 'settings_edit', 'uses' => 'SettingsController@editSettings'));

    Route::any( '/list/get', array('as' => 'list_get', 'uses' => 'ListController@getAction'));
    Route::post('/list/add', array('as' => 'list_add', 'uses' => 'ListController@createAction'));
    Route::post('/list/edit', array('as' => 'list_edit', 'uses' => 'ListController@editAction'));
    Route::post('/list/delete', array('as' => 'list_delete', 'uses' => 'ListController@deleteAction'));

    Route::post('/list/user/add', array('as' => 'list_user_add', 'uses' => 'ListController@addUserAction'));
    Route::post('/list/user/delete', array('as' => 'list_user_delete', 'uses' => 'ListController@deleteUserAction'));

    Route::post('/list/users/add', array('as' => 'list_users_add', 'uses' => 'ListController@addMultipleUserAction'));
  });
});

Route::filter('admin', function()
{
  if(!Auth::check() || !Auth::User()->isAdmin()) {
    return Redirect::home();
  }
});

Route::group(array('prefix' => 'admin', 'before' => 'admin'), function()
{
  Route::get('/', array('as' => 'admin_home', 'uses' => 'AdminController@indexAction'));

  Route::get('/news', array('as' => 'admin_news', 'uses' => 'AdminController@newsAction'));
  Route::post('/news/create', array('before' => 'csrf', 'as' => 'admin_news_create', 'uses' => 'AdminController@newsCreateAction'));

  Route::get('/news/edit/{newsId?}', array('as' => 'admin_news_edit', 'uses' => 'AdminController@newsEditAction'));
  Route::post('/news/edit', array('before' => 'csrf', 'as' => 'admin_news_post_edit', 'uses' => 'AdminController@newsPostEditAction'));

  Route::post('/news/delete', array('before' => 'csrf', 'as' => 'admin_news_delete', 'uses' => 'AdminController@newsDeleteAction'));
});

Route::get('/privacy', function()
{
    return View::make('main/privacy');
});



Route::get('/old', array('before' => 'auth', 'as' => 'old_data', 'uses' => 'OldDataController@indexAction'));
