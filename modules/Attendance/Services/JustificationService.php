<?php

namespace Modules\Attendance\Services;

use Modules\Attendance\Repositories\JustificationRepository;
use Modules\Attendance\Repositories\AttendanceRecordRepository;
use Modules\Attendance\Models\Justification;

class JustificationService
{
    public function __construct(
        private JustificationRepository $justificationRepository,
        private AttendanceRecordRepository $recordRepository,
    ) {}

    public function submitJustification(int $studentId, int $recordId, string $reason, ?string $supportingDocument = null): Justification
    {
        $record = $this->recordRepository->findById($recordId);
        if (!$record || $record->student_id !== $studentId) {
            throw new \Exception('Invalid attendance record');
        }

        return $this->justificationRepository->create([
            'student_id' => $studentId,
            'attendance_record_id' => $recordId,
            'reason' => $reason,
            'supporting_document' => $supportingDocument,
            'status' => 'pending',
        ]);
    }

    public function approveJustification(Justification $justification): Justification
    {
        $approved = $this->justificationRepository->approve($justification);

        $this->recordRepository->update($approved->record, [
            'status' => 'justified',
        ]);

        return $approved;
    }

    public function rejectJustification(Justification $justification, string $rejectionReason): Justification
    {
        return $this->justificationRepository->reject($justification, $rejectionReason);
    }

    public function getStudentJustifications(int $studentId, int $perPage = 25)
    {
        return $this->justificationRepository->findByStudent($studentId, $perPage);
    }

    public function getPendingJustifications(int $perPage = 25)
    {
        return $this->justificationRepository->findPending($perPage);
    }

    public function getApprovedJustificationsCount(int $studentId): int
    {
        return Justification::query()
            ->where('student_id', $studentId)
            ->where('status', 'approved')
            ->count();
    }

    public function deleteJustification(Justification $justification): bool
    {
        if ($justification->status !== 'pending') {
            throw new \Exception('Cannot delete reviewed justifications');
        }

        return $this->justificationRepository->delete($justification);
    }
}
