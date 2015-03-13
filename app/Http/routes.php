<?php

get('/', [
	'as' => 'home',
	'uses' => 'PagesController@indexPage'
]);

Route::group(['prefix' => 'auth'], function()
{
	get('/login', [
		'as' => 'auth.login',
		'uses' => 'LoginController@login'
	]);

	get('/logout', [
		'middleware' => 'auth',
		'as' => 'auth.logout',
		'uses' => 'LoginController@logout'
	]);
});

Route::group([
	'prefix' => 'admin',
	'middleware' => 'admin',
	'namespace' => 'Admin'
], function() {
	get('/', [
		'as' => 'admin.home',
		'uses' => 'MainController@index'
	]);
	
	get('/users', [
		'as' => 'admin.users',
		'uses' => 'DatabaseController@user'
	]);
	
	get('/profiles', [
		'as' => 'admin.profiles',
		'uses' => 'DatabaseController@profile'
	]);
});

get('/list/most', [
	'as' => 'tracked.most',
	'uses' => 'PagesController@mostTrackedPage'
]);