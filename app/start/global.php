<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

  app_path().'/commands',
  app_path().'/controllers',
  app_path().'/models',
  app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
  Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
  return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

App::bind('Hybrid_Auth', function() {
  return new Hybrid_Auth(array(
    // "base_url" => "http://vbanstatus.jung3o.com/login/auth",
    "base_url" => url('')."/login/auth",
    "providers" => array (
      "OpenID" => array (
              "enabled" => true
      ),
      "Steam" => array (
        "enabled" => true
      )
    )
  ));
});

App::error(function($exception, $code)
{

  $errorList = Array(
    "default" => Array($code, "Sorry."),
    403 => Array("Forbidden", "Sorry, you do not have access to this!"),
    404 => Array("Not Found", "Sorry, whatever you were looking for was not found!"),
    500 => Array("Server Error", "Something is wrong with the server. Please report this to Jung : jung3o@yahoo.com")
  );


  Log::warning("errorPage", array(
    "steamId" => Session::get('user.id'),
    "displayName" => Session::get('user.name'),
    "ipAddress" => Request::getClientIp(),
    "data" => Array(
      "code" => $code,
      "info" => $exception->getMessage(),
      "file" => $exception->getFile(),
      "line" => $exception->getLine()
    )
  ));

  $message = isset($errorList[$code]) ? $errorList[$code] : $errorList["default"];

  return Response::view('error', array('type' => $code, 'message' => $message), $code);
});
