<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Dashboard\Services\DashboardService;

class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService();
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dashboard');

        Livewire::component('dashboard::admin-dashboard', \Modules\Dashboard\Livewire\AdminDashboard::class);
    }
}
