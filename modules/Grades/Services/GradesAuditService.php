<?php

namespace Modules\Grades\Services;

use Modules\Audit\Services\AuditService;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAppeal;
use Modules\Grades\Models\Subject;
use Modules\Grades\Models\GradeAverage;

class GradesAuditService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function logGradeCreated(Grade $grade, ?string $notes = null): void
    {
        $this->auditService->logAction(
            'grade_created',
            'Grade',
            $grade->id,
            [
                'student_id' => $grade->student_id,
                'subject_id' => $grade->subject_id,
                'score' => $grade->score,
                'grade_type' => $grade->grade_type,
                'teacher_id' => $grade->teacher_id,
            ],
            "Grade created for student {$grade->student_id} in subject {$grade->subject_id}: {$grade->score}" . ($notes ? " ({$notes})" : '')
        );
    }

    public function logGradeUpdated(Grade $grade, array $changes): void
    {
        $this->auditService->logAction(
            'grade_updated',
            'Grade',
            $grade->id,
            $changes,
            "Grade updated for student {$grade->student_id}: " . implode(', ', array_keys($changes))
        );
    }

    public function logGradeDeleted(Grade $grade): void
    {
        $this->auditService->recordDeletedModel($grade, 'Grade deleted');
    }

    public function logBulkGradesImported(int $successCount, int $failCount, ?string $source = null): void
    {
        $this->auditService->logAction(
            'grades_bulk_imported',
            'Grade',
            null,
            [
                'success_count' => $successCount,
                'fail_count' => $failCount,
                'total' => $successCount + $failCount,
                'source' => $source ?? 'api',
            ],
            "Bulk grades imported: {$successCount} successful, {$failCount} failed" . ($source ? " from {$source}" : '')
        );
    }

    public function logGradeAverageCalculated(GradeAverage $average): void
    {
        $this->auditService->logAction(
            'grade_average_calculated',
            'GradeAverage',
            $average->id,
            [
                'student_id' => $average->student_id,
                'subject_id' => $average->subject_id,
                'average' => $average->average,
                'is_passed' => $average->is_passed,
            ],
            "Grade average calculated for student {$average->student_id}: {$average->average}"
        );
    }

    public function logGradeAppealSubmitted(GradeAppeal $appeal): void
    {
        $this->auditService->logAction(
            'grade_appeal_submitted',
            'GradeAppeal',
            $appeal->id,
            [
                'student_id' => $appeal->student_id,
                'grade_id' => $appeal->grade_id,
                'subject_id' => $appeal->subject_id,
                'reason' => $appeal->reason,
            ],
            "Grade appeal submitted by student {$appeal->student_id} for grade {$appeal->grade_id}"
        );
    }

    public function logGradeAppealApproved(GradeAppeal $appeal, ?string $response = null): void
    {
        $this->auditService->logAction(
            'grade_appeal_approved',
            'GradeAppeal',
            $appeal->id,
            [
                'student_id' => $appeal->student_id,
                'status' => 'approved',
                'response' => $response,
            ],
            "Grade appeal approved for student {$appeal->student_id}" . ($response ? ": {$response}" : '')
        );
    }

    public function logGradeAppealRejected(GradeAppeal $appeal, ?string $response = null): void
    {
        $this->auditService->logAction(
            'grade_appeal_rejected',
            'GradeAppeal',
            $appeal->id,
            [
                'student_id' => $appeal->student_id,
                'status' => 'rejected',
                'response' => $response,
            ],
            "Grade appeal rejected for student {$appeal->student_id}" . ($response ? ": {$response}" : '')
        );
    }

    public function logGradeAppealDeleted(GradeAppeal $appeal): void
    {
        $this->auditService->recordDeletedModel($appeal, 'Grade appeal deleted');
    }

    public function logSubjectCreated(Subject $subject): void
    {
        $this->auditService->logAction(
            'subject_created',
            'Subject',
            $subject->id,
            [
                'name' => $subject->name,
                'code' => $subject->code ?? null,
            ],
            "Subject created: {$subject->name}"
        );
    }

    public function logSubjectUpdated(Subject $subject, array $changes): void
    {
        $this->auditService->logAction(
            'subject_updated',
            'Subject',
            $subject->id,
            $changes,
            "Subject updated: {$subject->name}"
        );
    }

    public function logSubjectDeleted(Subject $subject): void
    {
        $this->auditService->recordDeletedModel($subject, 'Subject deleted');
    }

    public function logGradeExported(string $format, int $recordCount): void
    {
        $this->auditService->logAction(
            'grades_exported',
            'Grade',
            null,
            [
                'format' => $format,
                'record_count' => $recordCount,
            ],
            "Grades exported as {$format}: {$recordCount} records"
        );
    }

    public function logPermissionDenied(string $action, string $entityType, ?int $entityId = null): void
    {
        $this->auditService->logPermissionDenied($action, $entityType, $entityId);
    }
}
