<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Dashboard\Services\DashboardService;
use Modules\Dashboard\Services\StudentDashboardService;
use Modules\Dashboard\Services\ModuleAvailabilityService;
use App\Services\ModuleManager;

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

        $this->app->singleton(ModuleAvailabilityService::class, function ($app) {
            return new ModuleAvailabilityService($app->make(ModuleManager::class));
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dashboard');

        // Admin Dashboard Components
        Livewire::component('dashboard::admin-dashboard', \Modules\Dashboard\Livewire\AdminDashboard::class);

        // Student Dashboard Components
        Livewire::component('student-dashboard-main', \Modules\Dashboard\Livewire\StudentDashboard\StudentDashboardMain::class);
        Livewire::component('student-grades-section', \Modules\Dashboard\Livewire\StudentDashboard\StudentGradesSection::class);
        Livewire::component('student-attendance-section', \Modules\Dashboard\Livewire\StudentDashboard\StudentAttendanceSection::class);
        Livewire::component('student-billing-section', \Modules\Dashboard\Livewire\StudentDashboard\StudentBillingSection::class);
        Livewire::component('student-class-section', \Modules\Dashboard\Livewire\StudentDashboard\StudentClassSection::class);
        Livewire::component('chef-classe-section', \Modules\Dashboard\Livewire\StudentDashboard\ChefClasseSection::class);
    }
}
