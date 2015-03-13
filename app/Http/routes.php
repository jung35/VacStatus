<?php

get('/', [
	'as' => 'home',
	'uses' => 'MockUpController@indexPage'
]);

get('/login/{action?}', [
	'as' => 'login',
	'uses' => 'LoginController@login'
]);

get('/list/most', [
	'as' => 'tracked.most',
	'uses' => 'MockUpController@mostTrackedPage'
]);