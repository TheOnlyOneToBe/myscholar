<?php

namespace Modules\Classes\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Classes\Models\ClassModel;
use Modules\Classes\Policies\ClassPolicy;

class ClassesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->loadMigrations();
        $this->loadRoutes();
        $this->registerPolicies();
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

    private function registerPolicies(): void
    {
        Gate::policy(ClassModel::class, ClassPolicy::class);
    }
}
