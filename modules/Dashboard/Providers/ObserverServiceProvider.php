<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Dashboard\Observers\GradeObserver;
use Modules\Dashboard\Observers\AttendanceRecordObserver;
use Modules\Dashboard\Observers\InvoiceObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Enregistrer les observers pour invalider le cache automatiquement

        // Observer pour les notes
        \Modules\Grades\Models\Grade::observe(GradeObserver::class);

        // Observer pour les présences
        \Modules\Attendance\Models\AttendanceRecord::observe(AttendanceRecordObserver::class);

        // Observer pour les factures
        \Modules\Billing\Models\Invoice::observe(InvoiceObserver::class);
    }
}
