<?php

namespace Modules\Attendance\Repositories;

use Modules\Attendance\Models\AttendanceRecord;

class AttendanceRecordRepository
{
    public function all(int $perPage = 25)
    {
        return AttendanceRecord::query()
            ->paginate($perPage);
    }

    public function findById(int $id): ?AttendanceRecord
    {
        return AttendanceRecord::find($id);
    }

    public function findByStudent(int $studentId, int $perPage = 25)
    {
        return AttendanceRecord::query()
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findBySession(int $sessionId, int $perPage = 25)
    {
        return AttendanceRecord::query()
            ->where('attendance_session_id', $sessionId)
            ->paginate($perPage);
    }

    public function findBySessionAndStudent(int $sessionId, int $studentId): ?AttendanceRecord
    {
        return AttendanceRecord::query()
            ->where('attendance_session_id', $sessionId)
            ->where('student_id', $studentId)
            ->first();
    }

    public function findByStudentAndStatus(int $studentId, string $status, int $perPage = 25)
    {
        return AttendanceRecord::query()
            ->where('student_id', $studentId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): AttendanceRecord
    {
        return AttendanceRecord::create($data);
    }

    public function update(AttendanceRecord $record, array $data): AttendanceRecord
    {
        $record->update($data);
        return $record->refresh();
    }

    public function delete(AttendanceRecord $record): bool
    {
        return $record->delete();
    }

    public function getStudentAbsenceCount(int $studentId): int
    {
        return AttendanceRecord::query()
            ->where('student_id', $studentId)
            ->where('status', 'absent')
            ->count();
    }

    public function getStudentAttendanceRate(int $studentId, string $startDate = null, string $endDate = null): float
    {
        $query = AttendanceRecord::query()->where('student_id', $studentId);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $total = $query->count();
        if ($total === 0) {
            return 0;
        }

        $present = (clone $query)->where('status', 'present')->count();
        return ($present / $total) * 100;
    }
}
