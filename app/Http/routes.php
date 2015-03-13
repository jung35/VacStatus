<?php

Route::get('/', [
	'as' => 'home', 'uses' => 'MockUpController@indexPage'
]);

Route::get('/list/most', [
	'as' => 'tracked.most', 'uses' => 'MockUpController@mostTrackedPage'
]);