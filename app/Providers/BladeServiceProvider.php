<?php namespace VacStatus\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('setActiveLink', function($expression) {
            return "<?php echo Route::currentRouteName() == {$expression} ? \'active\' : \'\';  ?>";
        });
    }

    public function register()
    {
        //
    }
}