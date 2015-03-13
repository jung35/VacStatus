<?php namespace VacStatus\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
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

		$this->app->bind('Hybrid_Auth', function() {
			return new Hybrid_Auth(array(
				"base_url" => url('')."/login/auth",
				"providers" => array (
					"OpenID" => array (
						"enabled" => true
					),

					"Steam" => array (
						"enabled" => true,
						"wrapper" => array(
							'class'=>'Hybrid_Providers_Steam',
							'path' => __DIR__.'/../../vendor/hybridauth/hybridauth/additional-providers/hybridauth-steam/Providers/Steam.php'
						)
					)
				)
			));
		});
	}

}
