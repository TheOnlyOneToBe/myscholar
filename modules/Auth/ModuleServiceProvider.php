<?php

namespace Modules\Auth;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\Services\AuthService;
use Modules\Auth\Services\AccountLockingService;
use Modules\Auth\Services\PasswordResetService;
use Modules\Auth\Services\UserManagementService;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register module services
     */
    public function register(): void
    {
        // Register services as singletons
        $this->app->singleton(AccountLockingService::class, function ($app) {
            return new AccountLockingService();
        });

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                $app->make(AccountLockingService::class)
            );
        });

        $this->app->singleton(PasswordResetService::class, function ($app) {
            return new PasswordResetService();
        });

        $this->app->singleton(UserManagementService::class, function ($app) {
            return new UserManagementService();
        });
    }

    /**
     * Bootstrap module services
     */
    public function boot(): void
    {
        // Register routes
        $this->loadRoutes();

        // Register migrations
        $this->loadMigrations();

        // Register translations
        $this->loadTranslations();
    }

    /**
     * Load module routes
     */
    protected function loadRoutes(): void
    {
        $routeFile = __DIR__ . '/Routes/api.php';
        if (file_exists($routeFile)) {
            $this->loadRoutesFrom($routeFile);
        }
    }

    /**
     * Load module migrations
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    /**
     * Load module translations
     */
    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/translations', 'auth');
    }
}
