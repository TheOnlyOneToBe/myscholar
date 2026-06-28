<?php

namespace Modules\Attendance\Services;

use Modules\Attendance\Repositories\AttendanceRecordRepository;
use Modules\Attendance\Repositories\AttendanceSessionRepository;
use Modules\Auth\Models\User;
use Illuminate\Validation\ValidationException;
use DB;

class BulkAttendanceService
{
    public function __construct(
        private AttendanceRecordRepository $recordRepository,
        private AttendanceSessionRepository $sessionRepository,
    ) {}

    /**
     * Mark attendance for multiple students at once
     *
     * @param User $user
     * @param int $sessionId
     * @param array $records Array of ['student_id' => int, 'status' => string, 'notes' => string|null]
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     * @throws ValidationException
     */
    public function markBulkAttendance(User $user, int $sessionId, array $records): array
    {
        // Validate session exists and user has permission
        $session = $this->sessionRepository->findById($sessionId);
        if (!$session) {
            throw ValidationException::withMessages([
                'session_id' => 'Session not found',
            ]);
        }

        // Validate user has permission to mark attendance for this session
        if (!$user->can('create', \Modules\Attendance\Models\AttendanceRecord::class)) {
            throw ValidationException::withMessages([
                'permission' => 'You do not have permission to mark attendance',
            ]);
        }

        // Limit records per operation (prevent abuse)
        if (count($records) > 100) {
            throw ValidationException::withMessages([
                'records' => 'Maximum 100 records per bulk operation',
            ]);
        }

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        // Use transaction for consistency
        DB::beginTransaction();

        try {
            foreach ($records as $index => $record) {
                try {
                    // Validate record structure
                    if (!isset($record['student_id']) || !isset($record['status'])) {
                        throw new \Exception('Missing student_id or status');
                    }

                    // Validate status
                    $validStatuses = ['present', 'absent', 'late', 'excused', 'justified'];
                    if (!in_array($record['status'], $validStatuses)) {
                        throw new \Exception("Invalid status: {$record['status']}");
                    }

                    // Create or update attendance record
                    $attendanceRecord = $this->recordRepository->markAttendance(
                        $sessionId,
                        $record['student_id'],
                        $record['status'],
                        $record['notes'] ?? null,
                        $session->class_id
                    );

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'index' => $index,
                        'student_id' => $record['student_id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'bulk_operation' => 'Bulk operation failed: ' . $e->getMessage(),
            ]);
        }

        return $results;
    }

    /**
     * Get bulk operation summary
     *
     * @param int $sessionId
     * @return array
     */
    public function getBulkSummary(int $sessionId): array
    {
        $session = $this->sessionRepository->findById($sessionId);
        if (!$session) {
            return [];
        }

        $records = $this->recordRepository->findBySession($sessionId, 1000);

        $summary = [
            'session_id' => $sessionId,
            'total_students' => $records->total(),
            'marked' => 0,
            'unmarked' => 0,
            'by_status' => [],
        ];

        foreach ($records->items() as $record) {
            $summary['marked']++;
            $status = $record->status;
            $summary['by_status'][$status] = ($summary['by_status'][$status] ?? 0) + 1;
        }

        $summary['unmarked'] = $session->class->students()->count() - $summary['marked'];

        return $summary;
    }

    /**
     * Validate bulk records before processing
     *
     * @param array $records
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateBulkRecords(array $records): array
    {
        $errors = [];
        $validStatuses = ['present', 'absent', 'late', 'excused', 'justified'];

        foreach ($records as $index => $record) {
            if (!isset($record['student_id'])) {
                $errors[] = "Record {$index}: Missing student_id";
            }

            if (!isset($record['status'])) {
                $errors[] = "Record {$index}: Missing status";
            } elseif (!in_array($record['status'], $validStatuses)) {
                $errors[] = "Record {$index}: Invalid status '{$record['status']}'";
            }

            if (isset($record['notes']) && strlen($record['notes']) > 500) {
                $errors[] = "Record {$index}: Notes exceed 500 characters";
            }
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    /**
     * Get template for bulk import
     *
     * @return array
     */
    public function getBulkTemplate(): array
    {
        return [
            'columns' => ['student_id', 'status', 'notes'],
            'statuses' => ['present', 'absent', 'late', 'excused', 'justified'],
            'example' => [
                ['student_id' => 1, 'status' => 'present', 'notes' => ''],
                ['student_id' => 2, 'status' => 'absent', 'notes' => 'Medical appointment'],
                ['student_id' => 3, 'status' => 'late', 'notes' => 'Traffic delay'],
            ],
        ];
    }
}
