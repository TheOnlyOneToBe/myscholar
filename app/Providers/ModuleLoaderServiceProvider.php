<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleLoaderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Load module service providers
        $modules = [
            'Modules\Auth\Providers\AuthServiceProvider',
            'Modules\Config\Providers\ConfigServiceProvider',
            'Modules\Audit\Providers\AuditServiceProvider',
            'Modules\Notifications\Providers\NotificationServiceProvider',
            'Modules\Students\Providers\StudentsServiceProvider',
            'Modules\Attendance\Providers\AttendanceServiceProvider',
            'Modules\Grades\Providers\GradesServiceProvider',
            'Modules\Dashboard\Providers\DashboardServiceProvider',
            'Modules\Billing\Providers\BillingServiceProvider',
        ];

        foreach ($modules as $module) {
            if (class_exists($module)) {
                $this->app->register($module);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
