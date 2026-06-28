<?php

namespace Modules\Teachers\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TeacherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Charger les migrations
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        // Charger les routes
        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
