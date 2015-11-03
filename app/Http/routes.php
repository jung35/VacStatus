<?php

Route::model('userList', 'VacStatus\Models\UserList', function() { return ['error' => '404']; });
Route::model('news', 'VacStatus\Models\News', function() { return ['error' => '404']; });

post('/search', [ 'as' => 'search', 'uses' => 'DisplayController@searchPage' ]);

Route::group(['prefix' => 'auth'], function()
{
	get('/login', [ 'as' => 'auth.login', 'uses' => 'LoginController@sendToSteam' ]);
	get('/check', [ 'as' => 'auth.check', 'uses' => 'LoginController@handleSteamLogin' ]);
	get('/logout', [ 'middleware' => 'auth', 'as' => 'auth.logout', 'uses' => 'LoginController@logout' ]);
});

/**
 * API ROUTING STARTS HERE
 */

Route::group(['prefix' => 'api'], function()
{
	Route::group(['prefix' => 'v1', 'namespace' => 'APIv1'], function()
	{
		get('/', [ 'uses' => 'MainController@index']);
		get('/me', [ 'uses' => 'MainController@navbar']);
		get('/profile/{steamid}', [ 'uses' => 'ProfileController@index' ]);
		get('/search/{searchKey}', [ 'uses' => 'SearchController@search' ]);

		/**
		 * ROUTING CONTAINING ALL THE LISTS
		 */
		Route::group(['prefix' => 'list', 'namespace' => 'Lists'], function()
		{
			get('/', [ 'uses' => 'MainController@listPortal' ]);
			get('/simple', [ 'uses' => 'MainController@myLists' ]);
			get('/most', [ 'uses' => 'MostTrackedController@get' ]);
			get('/latest', [ 'uses' => 'LatestTrackedController@get' ]);
			get('/latest/vac', [ 'uses' => 'LatestVACBannedController@get' ]);
			get('/latest/game', [ 'uses' => 'LatestGameBannedController@get' ]);
			get('/{userList}', [ 'uses' => 'CustomListController@get' ]);

			Route::group(['middleware' => 'auth'], function()
			{
				Route::any('/add/many', [ 'uses' => 'CustomListController@addManyProfilesToList' ]);

				post('/add', [ 'uses' => 'CustomListController@addProfileToList' ]);
				post('/{listId?}', [ 'uses' => 'CustomListController@modify' ]);
				post('/subscribe/{userList}', [ 'uses' => 'CustomListController@subscribe' ]);
				delete('/subscribe/{userList}', [ 'uses' => 'CustomListController@unsubscribe' ]);
				delete('/delete', [ 'uses' => 'CustomListController@deleteProfileFromList' ]);
				delete('/{userList}', [ 'uses' => 'CustomListController@delete' ]);
			});
		});
		
		/**
		 * ROUTING CONTAINING NEWS
		 */
		Route::group(['prefix' => 'news'], function()
		{
			get('/', [ 'uses' => 'NewsController@index' ]);
			get('/{news}', [ 'uses' => 'NewsController@showArticle' ]);
		});

		/**
		 * ROUTING CONTAINING DONATIONS
		 */
		Route::group(['prefix' => 'donate'], function()
		{
			get('/', [ 'uses' => 'DonationController@index' ]);

			Route::any('/ipn', ['uses' => 'DonationController@IPNAction']);
		});

		Route::group(['prefix' => 'settings', 'middleware' => 'auth'], function()
		{
			Route::group(['prefix' => 'subscribe'], function()
			{
				get('/', [ 'uses' => 'SettingsController@subscribeIndex' ]);
				get('/{email}/{code}', [ 'uses' => 'SettingsController@subscriptionVerify' ]);
				post('/', [ 'uses' => 'SettingsController@makeSubscription' ]);
				delete('/email', [ 'uses' => 'SettingsController@deleteEmail' ]);
				delete('/pushbullet', [ 'uses' => 'SettingsController@deletePushBullet' ]);
			});


			Route::group(['prefix' => 'userkey'], function()
			{
				get('/', [ 'uses' => 'SettingsController@showUserKey' ]);
				post('/', [ 'uses' => 'SettingsController@newUserKey' ]);
			});
		});
	});
});


/**
 * ROUTING CONTAINING ANYTHING ADMIN
 */

Route::group([
	'prefix' => 'admin', 'middleware' => 'admin', 'namespace' => 'Admin'
], function() {

	get('/', [ 'as' => 'admin.home', 'uses' => 'MainController@index' ]);
	get('log/{filename}', [ 'as' => 'admin.log', 'uses' => 'MainController@viewLog' ]);
	post('/announcement', [ 'as' => 'admin.announcement.save', 'uses' => 'MainController@announcementSave' ]);

	Route::group(['prefix' => 'db'], function()
	{
		get('/', [ 'as' => 'admin.db', 'uses' => 'DatabaseController@index' ]);
		get('/users', [ 'as' => 'admin.db.users', 'uses' => 'DatabaseController@user' ]);
		get('/profiles', [ 'as' => 'admin.db.profiles', 'uses' => 'DatabaseController@profile' ]);
	});

	Route::group(['prefix' => 'news'], function()
	{
		get('/', [ 'as' => 'admin.news', 'uses' => 'NewsController@index' ]);
		get('/{news}', [ 'as' => 'admin.news.edit', 'uses' => 'NewsController@editForm' ]);
		post('/{newsId?}', [ 'as' => 'admin.news.save', 'uses' => 'NewsController@saveNews' ]);
		delete('/{news}', [ 'as' => 'admin.news.delete', 'uses' => 'NewsController@delete' ]);
	});

	Route::group(['prefix' => 'announcement'], function()
	{
		get('/', [ 'as' => 'admin.announcement', 'uses' => 'AnnouncementController@index' ]);
	});
});

Route::model('userList', 'VacStatus\Models\UserList', function() { return ['error' => '404']; });
Route::any('{undefinedRoute}', function ($undefinedRoute) {
    return view('app');
})->where('undefinedRoute', '([A-z\d-\/_.]+)?');

Event::listen('illuminate.query', function($query)
{
    // var_dump($query);
});