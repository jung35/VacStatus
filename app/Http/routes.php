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

get('/list/latest', [
	'as' => 'tracked.latest',
	'uses' => 'PagesController@latestTrackedPage'
]);

get('/list/{listId}', [
	'as' => 'tracked.custom',
	'uses' => 'PagesController@customListPage'
]);

Route::group(['middleware' => 'auth'], function()
{
	get('/list', [
		'as' => 'list.list',
		'uses' => 'PagesController@listListPage'
	]);
});

get('/list/{useless}/{listId}', function($soUSLESS, $listId) {
	return Redirect::route('tracked.custom', $listId, 301); 
});

get('/u/{steam65BitId}', [
	'as' => 'profile',
	'uses' => 'PagesController@profilePage'
]);

Route::group(['prefix' => 'api'], function()
{
	Route::group(['prefix' => 'v1', 'namespace' => 'APIv1'], function()
	{
		Route::group(['prefix' => 'list'], function()
		{
			get('/simple', [
				'middleware' => 'auth',
				'as' => 'api.v1.list.simple',
				'uses' => 'ListController@mySimpleList'
			]);

			get('/most', [
				'as' => 'api.v1.tracked.most',
				'uses' => 'ListController@mostTracked'
			]);

			get('/latest', [
				'as' => 'api.v1.tracked.latest',
				'uses' => 'ListController@latestTracked'
			]);

			get('/{userList}', [
				'as' => 'api.v1.tracked.latest',
				'uses' => 'ListController@customList'
			]);

			get('/', [
				'as' => 'api.v1.list.list',
				'uses' => 'ListController@listList'
			]);

			//
			//	---------------------------------
			//

			post('/add', [
				'as' => 'api.v1.list.user.add',
				'uses' => 'ListUserController@addToList'
			]);

			post('/{listId?}', [
				'as' => 'api.v1.list.create',
				'uses' => 'ListController@modifyCustomList'
			]);

			//
			//	---------------------------------
			//

			delete('/delete', [
				'as' => 'api.v1.list.user.delete',
				'uses' => 'ListUserController@deleteFromList'
			]);

			delete('/{userList}', [
				'as' => 'api.v1.tracked.latest',
				'uses' => 'ListController@deleteCustomList'
			]);
		});

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

Route::model('userList', 'VacStatus\Models\UserList', function()
{
	return ['error' => '404'];
});