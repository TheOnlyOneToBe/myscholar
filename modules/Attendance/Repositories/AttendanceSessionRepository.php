<?php

namespace Modules\Attendance\Repositories;

use Modules\Attendance\Models\AttendanceSession;
use Illuminate\Pagination\Paginator;

class AttendanceSessionRepository
{
    public function all(int $perPage = 25)
    {
        return AttendanceSession::query()
            ->paginate($perPage);
    }

    public function findById(int $id): ?AttendanceSession
    {
        return AttendanceSession::find($id);
    }

    public function findByClassAndDate(int $classId, string $date, int $perPage = 25)
    {
        return AttendanceSession::query()
            ->where('class_id', $classId)
            ->where('date', $date)
            ->paginate($perPage);
    }

    public function findByClass(int $classId, int $perPage = 25)
    {
        return AttendanceSession::query()
            ->where('class_id', $classId)
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    public function findBySubject(int $subjectId, int $perPage = 25)
    {
        return AttendanceSession::query()
            ->where('subject_id', $subjectId)
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): AttendanceSession
    {
        return AttendanceSession::create($data);
    }

    public function update(AttendanceSession $session, array $data): AttendanceSession
    {
        $session->update($data);
        return $session->refresh();
    }

    public function delete(AttendanceSession $session): bool
    {
        return $session->delete();
    }

    public function getSessionsForDateRange(int $classId, string $startDate, string $endDate, int $perPage = 25)
    {
        return AttendanceSession::query()
            ->where('class_id', $classId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }
}
