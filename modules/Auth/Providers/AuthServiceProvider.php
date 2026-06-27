<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Load views from the module
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'auth');

        // Load translations from the module
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'auth');

        // Load routes from web.php
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }
}
