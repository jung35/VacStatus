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
Route::post('/search', Array('as' => 'search_single', 'uses' => 'HomeController@searchSingleAction'));

Route::get('/login/{action?}', Array('as' => 'login', 'uses' => 'LoginController@loginAction'));
Route::get('/logout', Array('as' => 'logout', 'uses' => 'LoginController@logoutAction'));

Route::get('/u/{steam3Id?}', Array('as' => 'profile', 'uses' => 'ProfileController@profileAction'));

Route::post('/u/update/single/{steam3Id}', array('before' => 'csrf', 'uses' => 'ProfileController@updateSingleProfileAction'));
