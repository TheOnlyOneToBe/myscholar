<?php

namespace Modules\Reporting\Providers;

use Illuminate\Support\ServiceProvider;

class ReportingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerServices();
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerMigrations();
    }

    private function registerServices(): void
    {
        $this->app->singleton(
            'Modules\Reporting\Services\ReportService',
            'Modules\Reporting\Services\ReportService'
        );

        $this->app->singleton(
            'Modules\Reporting\Services\ExportService',
            'Modules\Reporting\Services\ExportService'
        );

        $this->app->singleton(
            'Modules\Reporting\Services\AnalyticsService',
            'Modules\Reporting\Services\AnalyticsService'
        );
    }

    private function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }

    private function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }
}
