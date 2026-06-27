<?php

namespace Modules\Config\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Config\Services\SchoolYearSessionService;
use Modules\Config\Helpers\ConfigHelper;
use Modules\Config\Middleware\InitializeSchoolYearSession;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerServices();
        $this->registerFacades();
    }

    public function boot(): void
    {
        $this->loadMigrations();
        $this->loadRoutes();
        $this->loadViews();
        $this->loadHelpers();
        $this->registerMiddleware();
    }

    private function registerServices(): void
    {
        // School Year Session Service
        $this->app->singleton(SchoolYearSessionService::class, function ($app) {
            return new SchoolYearSessionService();
        });

        // Config Helper
        $this->app->singleton('config-helper', function ($app) {
            return new ConfigHelper();
        });
    }

    private function registerFacades(): void
    {
        // Register Config Facade alias
        $this->app->alias('config-helper', ConfigHelper::class);
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }

    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'config');
    }

    private function loadHelpers(): void
    {
        $helperPath = __DIR__ . '/../helpers.php';
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    private function registerMiddleware(): void
    {
        // Register middleware globally or within the module
        // This will be hooked into the application's middleware stack
    }
}
