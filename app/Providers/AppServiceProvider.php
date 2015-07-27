<?php

namespace VacStatus\Providers;

use Illuminate\Support\ServiceProvider;

use VacStatus\Socialite\SteamProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'steam',
            function ($app) use ($socialite) {
                $config = $app['config']['services.steam'];
                return $socialite->buildProvider(SteamProvider::class, $config);
            }
        );
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'VacStatus\Services\Registrar'
		);
	}

}
