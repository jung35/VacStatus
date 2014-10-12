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

Route::get('/', Array('as' => 'home', 'uses' => 'HomeController@indexAction'));
Route::get('/l/{uorl?}/{list?}', Array('uses' => 'HomeController@indexAction'));

Route::get('/logout', Array('before' => 'auth', 'as' => 'logout', 'uses' => 'LoginController@logoutAction'));

Route::post('/profile_lookup', Array('as' => 'search_single', 'uses' => 'HomeController@searchSingleAction'));
Route::post('/search', Array('as' => 'search_multi', 'uses' => 'HomeController@searchMultipleAction'));

Route::get('/login/{action?}', Array('as' => 'login', 'uses' => 'LoginController@loginAction'));

Route::get('/u/{steam3Id?}', Array('as' => 'profile', 'uses' => 'ProfileController@profileAction'));

Route::post('/u/update/single', Array('before' => 'csrf', 'uses' => 'ProfileController@updateSingleProfileAction'));

Route::post('/list/fetch', Array('before' => 'csrf', 'as' => 'list_fetch', 'uses' => 'DisplayListController@fetchListAction'));
Route::post('/list/update', Array('before' => 'csrf', 'as' => 'list_update', 'uses' => 'DisplayListController@updateListAction'));

Route::any('/ipn', Array('uses' => 'DonationController@IPNAction'));
Route::any('/donation', Array('as' => 'donation', 'uses' => 'DonationController@DonationAction'));

Route::get('/news/{newsId?}', Array('as' => 'news', 'uses' => 'HomeController@newsAction'));

Route::group(array('before' => 'auth|csrf'), function() {

  Route::any( '/list/get', Array('as' => 'list_get', 'uses' => 'ListController@getAction'));
  Route::post('/list/add', Array('as' => 'list_add', 'uses' => 'ListController@createAction'));
  Route::post('/list/edit', Array('as' => 'list_edit', 'uses' => 'ListController@editAction'));
  Route::post('/list/delete', Array('as' => 'list_delete', 'uses' => 'ListController@deleteAction'));

  Route::post('/list/user/add', Array('as' => 'list_user_add', 'uses' => 'ListController@addUserAction'));
  Route::post('/list/user/delete', Array('as' => 'list_user_delete', 'uses' => 'ListController@deleteUserAction'));

});

Route::filter('admin', function()
{
  if(!Auth::check() || !Auth::User()->isAdmin()) {
    return Redirect::home();
  }
});

Route::group(array('prefix' => 'admin', 'before' => 'admin'), function()
{
  Route::get('/', Array('as' => 'admin_home', 'uses' => 'AdminController@indexAction'));

  Route::get('/news', Array('as' => 'admin_news', 'uses' => 'AdminController@newsAction'));
  Route::post('/news/create', Array('before' => 'csrf', 'as' => 'admin_news_create', 'uses' => 'AdminController@newsCreateAction'));

  Route::get('/news/edit/{newsId?}', Array('as' => 'admin_news_edit', 'uses' => 'AdminController@newsEditAction'));
  Route::post('/news/edit', Array('before' => 'csrf', 'as' => 'admin_news_post_edit', 'uses' => 'AdminController@newsPostEditAction'));

  Route::post('/news/delete', Array('before' => 'csrf', 'as' => 'admin_news_delete', 'uses' => 'AdminController@newsDeleteAction'));
});

Route::get('/privacy', function()
{
    return View::make('main/privacy');
});



Route::get('/old', Array('before' => 'auth', 'as' => 'old_data', 'uses' => 'OldDataController@indexAction'));
