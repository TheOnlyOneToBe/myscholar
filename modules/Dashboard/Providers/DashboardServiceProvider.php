<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate;
use Livewire\Livewire;
use Modules\Dashboard\Services\DashboardService;
use Modules\Dashboard\Services\StudentDashboardService;
use Modules\Dashboard\Services\ParentDashboardService;
use Modules\Dashboard\Services\ModuleAvailabilityService;
use Modules\Dashboard\Services\TermDocumentService;
use Modules\Dashboard\Services\BulletinPDFService;
use Modules\Dashboard\Policies\DocumentPolicy;
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

        $this->app->singleton(ParentDashboardService::class, function ($app) {
            return new ParentDashboardService();
        });

        $this->app->singleton(ModuleAvailabilityService::class, function ($app) {
            return new ModuleAvailabilityService($app->make(ModuleManager::class));
        });

        $this->app->singleton(BulletinPDFService::class, function ($app) {
            return new BulletinPDFService();
        });

        $this->app->singleton(TermDocumentService::class, function ($app) {
            return new TermDocumentService(
                $app->make(\Modules\Grades\Services\TermGradeService::class),
                $app->make(BulletinPDFService::class)
            );
        });
    }

    public function boot(Gate $gate): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dashboard');

        // Register Policies for Document Authorization
        $gate->define('downloadSchoolCertificate', function ($user, $academicYearId, $student) {
            return (new DocumentPolicy())->downloadSchoolCertificate($user, $academicYearId, $student);
        });

        $gate->define('downloadReportCard', function ($user, $academicYearId, $student) {
            return (new DocumentPolicy())->downloadReportCard($user, $academicYearId, $student);
        });

        $gate->define('downloadTranscript', function ($user, $student) {
            return (new DocumentPolicy())->downloadTranscript($user, $student);
        });

        $gate->define('downloadEnrollmentSummary', function ($user, $student) {
            return (new DocumentPolicy())->downloadEnrollmentSummary($user, $student);
        });

        $gate->define('downloadInvoice', function ($user, $invoiceId, $student) {
            return (new DocumentPolicy())->downloadInvoice($user, $invoiceId, $student);
        });

        // Admin Dashboard Components
        Livewire::component('dashboard::admin-dashboard', \Modules\Dashboard\Livewire\AdminDashboard::class);

        // Student Dashboard Components
        Livewire::component('student-dashboard-main', \Modules\Dashboard\Livewire\StudentDashboard\StudentDashboardMain::class);
        Livewire::component('student-navbar', \Modules\Dashboard\Livewire\StudentDashboard\StudentNavbar::class);
        Livewire::component('student-sidebar', \Modules\Dashboard\Livewire\StudentDashboard\StudentSidebar::class);
        Livewire::component('student-grades-section', \Modules\Dashboard\Livewire\StudentDashboard\StudentGradesSection::class);
        Livewire::component('student-attendance-section', \Modules\Dashboard\Livewire\StudentDashboard\StudentAttendanceSection::class);
        Livewire::component('student-billing-section', \Modules\Dashboard\Livewire\StudentDashboard\StudentBillingSection::class);
        Livewire::component('student-class-section', \Modules\Dashboard\Livewire\StudentDashboard\StudentClassSection::class);
        Livewire::component('chef-classe-section', \Modules\Dashboard\Livewire\StudentDashboard\ChefClasseSection::class);
        Livewire::component('student-profile-section', \Modules\Dashboard\Livewire\StudentDashboard\StudentProfileSection::class);

        // Parent Dashboard Components
        Livewire::component('parent-dashboard-main', \Modules\Dashboard\Livewire\ParentDashboard\ParentDashboardMain::class);
        Livewire::component('parent-navbar', \Modules\Dashboard\Livewire\ParentDashboard\ParentNavbar::class);
        Livewire::component('parent-sidebar', \Modules\Dashboard\Livewire\ParentDashboard\ParentSidebar::class);
        Livewire::component('parent-children-section', \Modules\Dashboard\Livewire\ParentDashboard\ParentChildrenSection::class);
        Livewire::component('parent-grades-section', \Modules\Dashboard\Livewire\ParentDashboard\ParentGradesSection::class);
        Livewire::component('parent-attendance-section', \Modules\Dashboard\Livewire\ParentDashboard\ParentAttendanceSection::class);
        Livewire::component('parent-billing-section', \Modules\Dashboard\Livewire\ParentDashboard\ParentBillingSection::class);
        Livewire::component('parent-bulletin-section', \Modules\Dashboard\Livewire\ParentDashboard\ParentBulletinSection::class);
        Livewire::component('parent-alerts-section', \Modules\Dashboard\Livewire\ParentDashboard\ParentAlertsSection::class);
    }
}
