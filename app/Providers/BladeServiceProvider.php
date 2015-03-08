<?php namespace VacStatus\Providers;

use Illuminate\Support\ServiceProvider;
use \Blade;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::extend(function($view, $compiler)
        {
            $pattern = $compiler->createOpenMatcher('setActiveLink');

            return preg_replace($pattern, '$1<?php echo Route::currentRouteName() == $2) ? \'class="active"\' : \'\';  ?>', $view);
        });
    }

    public function register()
    {
        //
    }
}