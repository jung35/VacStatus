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

Route::post('/search', Array('as' => 'search_single', 'uses' => 'HomeController@searchSingleAction'));

Route::get('/login/{action?}', Array('as' => 'login', 'uses' => 'LoginController@loginAction'));

Route::get('/u/{steam3Id?}', Array('as' => 'profile', 'uses' => 'ProfileController@profileAction'));

Route::post('/u/update/single', Array('before' => 'csrf', 'uses' => 'ProfileController@updateSingleProfileAction'));

Route::post('/list/fetch', Array('before' => 'csrf', 'as' => 'list_fetch', 'uses' => 'DisplayListController@fetchListAction'));

Route::group(array('before' => 'auth|csrf'), function() {

  Route::post('/list/add', Array('as' => 'list_add', 'uses' => 'ListController@createAction'));
  Route::post('/list/edit', Array('as' => 'list_edit', 'uses' => 'ListController@editAction'));
  Route::post('/list/delete', Array('as' => 'list_delete', 'uses' => 'ListController@deleteAction'));

  Route::post('/list/user/add', Array('as' => 'list_user_add', 'uses' => 'ListController@addUserAction'));
  Route::post('/list/user/delete', Array('as' => 'list_user_delete', 'uses' => 'ListController@deleteUserAction'));

});
