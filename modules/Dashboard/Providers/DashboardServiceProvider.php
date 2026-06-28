<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Dashboard\Services\DashboardService;
use Modules\Dashboard\Services\StudentDashboardService;

class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService();
        });

        $this->app->singleton(StudentDashboardService::class, function ($app) {
            return new StudentDashboardService();
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dashboard');

        Livewire::component('dashboard::admin-dashboard', \Modules\Dashboard\Livewire\AdminDashboard::class);
    }
}
