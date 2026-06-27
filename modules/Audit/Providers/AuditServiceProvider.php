<?php

namespace Modules\Audit\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Audit\Services\AuditService;
use Modules\Audit\Listeners\LogExceptionListener;
use Illuminate\Foundation\Events\LocalizedExceptionRendered;

class AuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerServices();
    }

    public function boot(): void
    {
        $this->loadMigrations();
        $this->loadRoutes();
        $this->loadTranslations();
        $this->registerListeners();
    }

    private function registerServices(): void
    {
        $this->app->singleton(AuditService::class, function ($app) {
            return new AuditService();
        });
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }

    private function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'audit');
    }

    private function registerListeners(): void
    {
        $this->app['events']->listen(
            LocalizedExceptionRendered::class,
            LogExceptionListener::class
        );
    }
}
