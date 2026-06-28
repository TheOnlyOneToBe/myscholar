<?php

namespace Modules\Attendance\Services;

use Modules\Attendance\Repositories\AttendanceSessionRepository;
use Modules\Attendance\Repositories\AttendanceRecordRepository;
use Modules\Attendance\Repositories\AbsenceRepository;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Attendance\Models\AttendanceRecord;

class AttendanceService
{
    public function __construct(
        private AttendanceSessionRepository $sessionRepository,
        private AttendanceRecordRepository $recordRepository,
        private AbsenceRepository $absenceRepository,
    ) {}

    public function createSession(array $data): AttendanceSession
    {
        return $this->sessionRepository->create($data);
    }

    public function updateSession(AttendanceSession $session, array $data): AttendanceSession
    {
        return $this->sessionRepository->update($session, $data);
    }

    public function deleteSession(AttendanceSession $session): bool
    {
        return $this->sessionRepository->delete($session);
    }

    public function markAttendance(int $sessionId, int $studentId, string $status, ?string $notes = null): AttendanceRecord
    {
        $existing = $this->recordRepository->findBySessionAndStudent($sessionId, $studentId);

        $data = [
            'attendance_session_id' => $sessionId,
            'student_id' => $studentId,
            'status' => $status,
            'notes' => $notes,
        ];

        if ($existing) {
            return $this->recordRepository->update($existing, $data);
        }

        return $this->recordRepository->create($data);
    }

    public function getSessionAttendanceReport(int $sessionId)
    {
        $session = $this->sessionRepository->findById($sessionId);
        if (!$session) {
            return null;
        }

        $records = $this->recordRepository->findBySession($sessionId, 1000);

        return [
            'session' => $session,
            'total_records' => $records->total(),
            'attendance_rate' => $session->getAttendanceRate(),
            'records' => $records->items(),
        ];
    }

    public function getStudentAttendanceHistory(int $studentId, int $perPage = 25)
    {
        return $this->recordRepository->findByStudent($studentId, $perPage);
    }

    public function calculateStudentAttendanceRate(int $studentId, ?string $startDate = null, ?string $endDate = null): float
    {
        return $this->recordRepository->getStudentAttendanceRate($studentId, $startDate, $endDate);
    }

    public function updateAbsenceCounter(int $studentId): array
    {
        $absences = $this->recordRepository->getStudentAbsenceCount($studentId);

        $justifications = \Modules\Attendance\Models\Justification::query()
            ->where('student_id', $studentId)
            ->where('status', 'approved')
            ->count();

        $unjustified = $absences - $justifications;

        $counter = $this->absenceRepository->createOrUpdateCounter($studentId, $absences, $unjustified);

        return [
            'total_absences' => $absences,
            'justified_absences' => $justifications,
            'unjustified_absences' => $unjustified,
            'counter' => $counter,
        ];
    }

    public function checkAbsenceThresholds(int $studentId, int $threshold = 10): array
    {
        $stats = $this->updateAbsenceCounter($studentId);
        $alerts = [];

        if ($stats['total_absences'] >= $threshold) {
            $alert = $this->absenceRepository->createAlert(
                $studentId,
                'Student has reached maximum absence threshold',
                $threshold
            );
            $alerts[] = $alert;
        }

        if ($stats['unjustified_absences'] > ($threshold / 2)) {
            $alert = $this->absenceRepository->createAlert(
                $studentId,
                'Student has too many unjustified absences',
                (int)($threshold / 2)
            );
            $alerts[] = $alert;
        }

        return $alerts;
    }

    public function getClassAttendanceOverview(int $classId, string $date): array
    {
        $sessions = $this->sessionRepository->findByClassAndDate($classId, $date, 1000);

        $overview = [
            'class_id' => $classId,
            'date' => $date,
            'sessions' => [],
            'overall_attendance_rate' => 0,
        ];

        $totalPresent = 0;
        $totalRecords = 0;

        foreach ($sessions->items() as $session) {
            $rate = $session->getAttendanceRate();
            $overview['sessions'][] = [
                'session_id' => $session->id,
                'subject_id' => $session->subject_id,
                'attendance_rate' => $rate,
                'time' => $session->start_time ? $session->start_time->format('H:i') : null,
            ];

            $records = $this->recordRepository->findBySession($session->id, 1000);
            $totalRecords += $records->total();
            $totalPresent += $records->where('status', 'present')->count();
        }

        if ($totalRecords > 0) {
            $overview['overall_attendance_rate'] = ($totalPresent / $totalRecords) * 100;
        }

        return $overview;
    }
}
