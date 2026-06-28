<?php

namespace Modules\Attendance\Repositories;

use Modules\Attendance\Models\AbsenceCounter;
use Modules\Attendance\Models\AbsenceAlert;

class AbsenceRepository
{
    public function getAbsenceCounter(int $studentId): ?AbsenceCounter
    {
        return AbsenceCounter::where('student_id', $studentId)->first();
    }

    public function createOrUpdateCounter(int $studentId, int $totalAbsences, int $unjustifiedAbsences): AbsenceCounter
    {
        return AbsenceCounter::updateOrCreate(
            ['student_id' => $studentId],
            [
                'total_absences' => $totalAbsences,
                'unjustified_absences' => $unjustifiedAbsences,
            ]
        );
    }

    public function getStudentAbsenceAlerts(int $studentId, int $perPage = 25)
    {
        return AbsenceAlert::query()
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getPendingAlerts(int $perPage = 25)
    {
        return AbsenceAlert::query()
            ->where('is_acknowledged', false)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    public function createAlert(int $studentId, string $reason, int $threshold = null): AbsenceAlert
    {
        return AbsenceAlert::create([
            'student_id' => $studentId,
            'reason' => $reason,
            'absence_threshold' => $threshold,
        ]);
    }

    public function acknowledgeAlert(AbsenceAlert $alert): AbsenceAlert
    {
        $alert->update(['is_acknowledged' => true, 'acknowledged_at' => now()]);
        return $alert->refresh();
    }

    public function getAlertsByReason(string $reason, int $perPage = 25)
    {
        return AbsenceAlert::query()
            ->where('reason', $reason)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
