<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate;
use Modules\Auth\Models\User;
use Modules\Auth\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Gate $gate): void
    {
        // Register policies
        $gate->policy(User::class, UserPolicy::class);

        // Load views from the module
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'auth');

        // Load translations from the module
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'auth');

        // Load routes from web.php
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }
}
