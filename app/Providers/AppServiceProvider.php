<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PasswordService;
use App\Services\PermissionService;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load Auth module views - register the livewire and layouts subdirectories separately
        $viewsPath = base_path('modules/Auth/Resources/views');
        if (is_dir($viewsPath)) {
            // Register all views from the views directory with 'auth' namespace
            // Views will be accessed like: view('auth::livewire.login') or view('auth.livewire.login')
            $this->loadViewsFrom($viewsPath, 'auth');
        }

        // Load web routes for Auth module
        if (file_exists(base_path('modules/Auth/Routes/web.php'))) {
            $this->loadRoutesFrom(base_path('modules/Auth/Routes/web.php'));
        }

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
