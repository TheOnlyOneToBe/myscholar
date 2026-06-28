<?php

namespace Modules\Attendance;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\Justification;
use Modules\Attendance\Models\AbsenceAlert;
use Modules\Attendance\Policies\AttendanceSessionPolicy;
use Modules\Attendance\Policies\AttendanceRecordPolicy;
use Modules\Attendance\Policies\JustificationPolicy;
use Modules\Attendance\Policies\AbsenceAlertPolicy;
use Modules\Attendance\Repositories\AttendanceSessionRepository;
use Modules\Attendance\Repositories\AttendanceRecordRepository;
use Modules\Attendance\Repositories\JustificationRepository;
use Modules\Attendance\Repositories\AbsenceRepository;
use Modules\Attendance\Services\AttendanceService;
use Modules\Attendance\Services\JustificationService;

class AttendanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register repositories
        $this->app->singleton(AttendanceSessionRepository::class);
        $this->app->singleton(AttendanceRecordRepository::class);
        $this->app->singleton(JustificationRepository::class);
        $this->app->singleton(AbsenceRepository::class);

        // Register services
        $this->app->singleton(AttendanceService::class, function ($app) {
            return new AttendanceService(
                $app->make(AttendanceSessionRepository::class),
                $app->make(AttendanceRecordRepository::class),
                $app->make(AbsenceRepository::class),
            );
        });

        $this->app->singleton(JustificationService::class, function ($app) {
            return new JustificationService(
                $app->make(JustificationRepository::class),
                $app->make(AttendanceRecordRepository::class),
            );
        });
    }

    public function boot(): void
    {
        // Register policies
        Gate::policy(AttendanceSession::class, AttendanceSessionPolicy::class);
        Gate::policy(AttendanceRecord::class, AttendanceRecordPolicy::class);
        Gate::policy(Justification::class, JustificationPolicy::class);
        Gate::policy(AbsenceAlert::class, AbsenceAlertPolicy::class);

        // Load migrations, routes, views
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'attendance');
    }
}
