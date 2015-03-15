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

get('/list/most', [
	'as' => 'tracked.most',
	'uses' => 'PagesController@mostTrackedPage'
]);

get('/u/{steam65BitId}', [
	'as' => 'profile',
	'uses' => 'PagesController@profilePage'
]);

Route::group(['prefix' => 'api'], function()
{
	Route::group(['prefix' => 'v1', 'namespace' => 'APIv1'], function()
	{
		get('/profile/{steam65BitId}', [
		    'as' => 'api.v1.profile',
		    'uses' => 'ProfileController@index'
		]);
	});
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

	get('/db', [
	    'as' => 'admin.db',
	    'uses' => 'DatabaseController@index'
	]);
	
	get('/db/users', [
		'as' => 'admin.db.users',
		'uses' => 'DatabaseController@user'
	]);
	
	get('/db/profiles', [
		'as' => 'admin.db.profiles',
		'uses' => 'DatabaseController@profile'
	]);
});