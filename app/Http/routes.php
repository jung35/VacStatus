<?php

get('/', function() {
	return view('pages.home');
});

Route::group(['prefix' => 'auth'], function()
{
	get('/login', [ 'as' => 'auth.login', 'uses' => 'LoginController@sendToSteam' ]);
	get('/check', [ 'as' => 'auth.check', 'uses' => 'LoginController@handleSteamLogin' ]);
	get('/logout', [ 'middleware' => 'auth', 'as' => 'auth.logout', 'uses' => 'LoginController@logout' ]);
});

// Route::group(['prefix' => 'list'], function()
// {
// 	get('/', [ 'as' => 'list.list', 'uses' => 'PagesController@listListPage' ]);
// 	get('/most', [ 'as' => 'tracked.most', 'uses' => 'PagesController@mostTrackedPage' ]);

// 	Route::group(['prefix' => 'latest'], function()
// 	{
// 		get('/', [ 'as' => 'tracked.latest', 'uses' => 'PagesController@latestTrackedPage' ]);
// 		get('/vac', [ 'as' => 'tracked.latest.vac', 'uses' => 'PagesController@latestVACPage' ]);
// 		get('/game', [ 'as' => 'tracked.latest.game', 'uses' => 'PagesController@latestGameBanPage' ]);
// 	});

// 	get('/{listId}', [ 'as' => 'tracked.custom', 'uses' => 'PagesController@customListPage' ]);
// });

// get('/u/{steamid}', [ 'as' => 'profile', 'uses' => 'PagesController@profilePage' ]);
// get('/news/{p?}', [ 'as' => 'news', 'uses' => 'PagesController@newsPage']);
// get('/privacy', [ 'as' => 'privacy', 'uses' => 'PagesController@privacyPage' ]);
// get('/contact', [ 'as' => 'contact', 'uses' => 'PagesController@contactPage' ]);
// get('/donate', [ 'as' => 'donate', 'uses' => 'PagesController@donatePage' ]);

// post('/search', [ 'as' => 'search', 'uses' => 'PagesController@searchPage' ]);

// Route::group(['prefix' => 'settings'], function()
// {
// 	get('/', [ 'middleware' => 'auth', 'as' => 'settings', 'uses' => 'SettingsController@subscriptionPage' ]);
// 	get('/subscribe/{email}/{verify}', [ 'as' => 'settings.subscription.verify', 'uses' => 'SettingsController@subscriptionVerify' ]);
// });


/**
 * API ROUTING STARTS HERE
 */

Route::group(['prefix' => 'api'], function()
{
	Route::group(['prefix' => 'v1', 'namespace' => 'APIv1'], function()
	{
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
			get('/latest_vac', [ 'uses' => 'LatestVACBannedController@get' ]);
			get('/latest_game_ban', [ 'uses' => 'LatestGameBannedController@get' ]);
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
});

Route::model('userList', 'VacStatus\Models\UserList', function() { return ['error' => '404']; });
Route::model('news', 'VacStatus\Models\News', function() { return ['error' => '404']; });

Route::any('{undefinedRoute}', function ($undefinedRoute) {
    return view('pages.home');
})->where('undefinedRoute', '([A-z\d-\/_.]+)?');

// Event::listen('illuminate.query', function($query)
// {
//     var_dump($query);
// });