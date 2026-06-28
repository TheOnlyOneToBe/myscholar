<?php

namespace Modules\Grades\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAppeal;
use Modules\Grades\Models\Subject;
use Modules\Grades\Policies\GradePolicy;
use Modules\Grades\Policies\GradeAppealPolicy;
use Modules\Grades\Policies\SubjectPolicy;
use Modules\Grades\Services\GradesAuditService;
use Modules\Audit\Services\AuditService;

class GradesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('grades.service', function ($app) {
            return new \Modules\Grades\Services\GradeService(
                new \Modules\Grades\Repositories\GradeRepository()
            );
        });

        // Register audit service
        $this->app->singleton(GradesAuditService::class, function ($app) {
            return new GradesAuditService($app->make(AuditService::class));
        });
    }

    public function boot(): void
    {
        // Register policies
        Gate::policy(Grade::class, GradePolicy::class);
        Gate::policy(GradeAppeal::class, GradeAppealPolicy::class);
        Gate::policy(Subject::class, SubjectPolicy::class);

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'grades');
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'grades');

        Livewire::component('grades::grade-list', \Modules\Grades\Livewire\GradeListComponent::class);
        Livewire::component('grades::student-grades', \Modules\Grades\Livewire\StudentGradesComponent::class);
        Livewire::component('grades::subject-management', \Modules\Grades\Livewire\SubjectManagementComponent::class);
        Livewire::component('grades::class-statistics', \Modules\Grades\Livewire\ClassStatisticsComponent::class);
        Livewire::component('grades::grade-appeal', \Modules\Grades\Livewire\GradeAppealComponent::class);
    }
}
