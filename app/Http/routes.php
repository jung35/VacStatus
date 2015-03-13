<?php

get('/', [
	'as' => 'home',
	'uses' => 'MockUpController@indexPage'
]);

Route::group(['prefix' => 'auth'], function()
{
	get('/login', [
		'as' => 'auth.login',
		'uses' => 'LoginController@login'
	]);

	get('/logout', [
		'before' => 'auth',
		'as' => 'auth.logout',
		'uses' => 'LoginController@logout'
	]);
});

get('/list/most', [
	'as' => 'tracked.most',
	'uses' => 'MockUpController@mostTrackedPage'
]);