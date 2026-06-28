<?php

namespace Modules\Attendance\Repositories;

use Modules\Attendance\Models\Justification;

class JustificationRepository
{
    public function all(int $perPage = 25)
    {
        return Justification::query()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Justification
    {
        return Justification::find($id);
    }

    public function findByStudent(int $studentId, int $perPage = 25)
    {
        return Justification::query()
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByAttendanceRecord(int $recordId): ?Justification
    {
        return Justification::query()
            ->where('attendance_record_id', $recordId)
            ->first();
    }

    public function findByStatus(string $status, int $perPage = 25)
    {
        return Justification::query()
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findPending(int $perPage = 25)
    {
        return Justification::query()
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    public function create(array $data): Justification
    {
        return Justification::create($data);
    }

    public function update(Justification $justification, array $data): Justification
    {
        $justification->update($data);
        return $justification->refresh();
    }

    public function delete(Justification $justification): bool
    {
        return $justification->delete();
    }

    public function approve(Justification $justification): Justification
    {
        return $this->update($justification, [
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    public function reject(Justification $justification, string $rejectionReason = null): Justification
    {
        return $this->update($justification, [
            'status' => 'rejected',
            'rejection_reason' => $rejectionReason,
            'reviewed_at' => now(),
        ]);
    }
}
