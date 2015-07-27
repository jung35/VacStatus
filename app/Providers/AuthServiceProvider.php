<?php

namespace VacStatus\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;

use VacStatus\Socialite\SteamProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('steam', function($app) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new SteamProvider($app['request'], null, null, null);
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}