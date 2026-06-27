<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PasswordService;
use App\Services\PermissionService;
use App\Services\AlertService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PasswordService::class, function () {
            return new PasswordService();
        });

        $this->app->singleton(PermissionService::class, function () {
            return new PermissionService();
        });

        $this->app->singleton(AlertService::class, function () {
            return new AlertService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load helpers
        require_once __DIR__ . '/../helpers.php';

        // Enregistrer les commandes personnalisées
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\HashPassword::class,
                \App\Console\Commands\SyncPermissions::class,
            ]);
        }

        // Helper globaux pour les permissions dans les templates Blade
        \Illuminate\Support\Facades\Blade::if('can', function (string $permission) {
            return auth()->check() && auth()->user()->can($permission);
        });

        \Illuminate\Support\Facades\Blade::if('hasRole', function (string $role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        \Illuminate\Support\Facades\Blade::if('canAny', function (array $permissions) {
            return auth()->check() && auth()->user()->canAny($permissions);
        });
    }
}
