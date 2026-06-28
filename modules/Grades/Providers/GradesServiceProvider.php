<?php

namespace Modules\Grades\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class GradesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('grades.service', function ($app) {
            return new \Modules\Grades\Services\GradeService(
                new \Modules\Grades\Repositories\GradeRepository()
            );
        });
    }

    public function boot(): void
    {
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
