<?php

namespace Modules\Students\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentEnrollment;
use Modules\Students\Policies\StudentPolicy;
use Modules\Students\Policies\EnrollmentPolicy;
use Modules\Students\Services\StudentService;
use Modules\Students\Services\StudentIdService;

class StudentServiceProvider extends ServiceProvider
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
        $this->registerPolicies();
    }

    private function registerServices(): void
    {
        $this->app->singleton(StudentService::class, function ($app) {
            return new StudentService();
        });

        $this->app->singleton(StudentIdService::class, function ($app) {
            return new StudentIdService();
        });
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

    private function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'students');
    }

    private function registerPolicies(): void
    {
        $this->app['auth']->policy(Student::class, StudentPolicy::class);
        $this->app['auth']->policy(StudentEnrollment::class, EnrollmentPolicy::class);
    }
}
