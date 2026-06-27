<?php

namespace Modules\Notifications\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Notifications\Services\NotificationService;

class NotificationServiceProvider extends ServiceProvider
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
    }

    private function registerServices(): void
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
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
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'notifications');
    }
}
