<?php

namespace Steam\ServiceProvider;

use Illuminate\Support\ServiceProvider;

class SteamServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('steam', function()
        {
            return new Steam;
        });
    }

}
