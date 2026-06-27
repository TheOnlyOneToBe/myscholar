<?php

namespace Modules\Config\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Config\Services\SchoolYearSessionService;
use Modules\Config\Middleware\InitializeSchoolYearSession;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerServices();
    }

    public function boot(): void
    {
        $this->loadHelpers();
        $this->registerMiddleware();
    }

    private function registerServices(): void
    {
        $this->app->singleton(SchoolYearSessionService::class, function ($app) {
            return new SchoolYearSessionService();
        });
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
