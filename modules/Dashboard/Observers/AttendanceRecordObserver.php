<?php

namespace Modules\Dashboard\Observers;

use Modules\Attendance\Models\AttendanceRecord;
use Modules\Dashboard\Services\CacheManagementService;

class AttendanceRecordObserver
{
    public function created(AttendanceRecord $record): void
    {
        CacheManagementService::invalidateAttendanceCache();

        if ($record->student) {
            CacheManagementService::invalidateClassCache(
                $record->student->getCurrentClass()?->id
            );
        }
    }

    public function updated(AttendanceRecord $record): void
    {
        CacheManagementService::invalidateAttendanceCache();

        if ($record->student) {
            CacheManagementService::invalidateClassCache(
                $record->student->getCurrentClass()?->id
            );
        }
    }

    public function deleted(AttendanceRecord $record): void
    {
        CacheManagementService::invalidateAttendanceCache();

        if ($record->student) {
            CacheManagementService::invalidateClassCache(
                $record->student->getCurrentClass()?->id
            );
        }
    }
}
