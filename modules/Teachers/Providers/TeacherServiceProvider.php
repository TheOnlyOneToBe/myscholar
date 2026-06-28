<?php

namespace Modules\Teachers\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Modules\Teachers\Models\TeacherApplication;
use Modules\Teachers\Policies\TeacherApplicationPolicy;
use Modules\Teachers\Policies\TeacherPolicy;
use Modules\Teachers\Models\Teacher;

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

        // Charger les vues
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'teachers');

        // Charger les routes
        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/../Routes/api.php');

        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');

        // Enregistrer les policies
        $this->registerPolicies();

        // Définir les gates
        $this->registerGates();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Teacher::class, TeacherPolicy::class);
        Gate::policy(TeacherApplication::class, TeacherApplicationPolicy::class);
    }

    protected function registerGates(): void
    {
        Gate::define('review-teacher-applications', function ($user) {
            return $user->hasAnyRole(['super_administrator', 'proviseur', 'censeur']);
        });

        Gate::define('approve-teacher-application', function ($user) {
            return $user->hasAnyRole(['super_administrator', 'proviseur', 'censeur']);
        });
    }
}
