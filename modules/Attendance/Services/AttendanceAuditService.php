<?php

namespace Modules\Attendance\Services;

use Modules\Audit\Services\AuditService;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Attendance\Models\Justification;
use Modules\Attendance\Models\AbsenceAlert;

class AttendanceAuditService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function logSessionCreated(AttendanceSession $session, ?array $data = null): void
    {
        $this->auditService->logAction(
            'attendance_session_created',
            'AttendanceSession',
            $session->id,
            [
                'date' => $session->date,
                'class_id' => $session->class_id,
                'subject_id' => $session->subject_id,
            ],
            "Attendance session created for {$session->date}"
        );
    }

    public function logSessionUpdated(AttendanceSession $session, array $changes): void
    {
        $this->auditService->logAction(
            'attendance_session_updated',
            'AttendanceSession',
            $session->id,
            $changes,
            "Attendance session updated"
        );
    }

    public function logSessionDeleted(AttendanceSession $session): void
    {
        $this->auditService->recordDeletedModel($session, 'Attendance session deleted');
    }

    public function logAttendanceMarked(AttendanceRecord $record, ?string $notes = null): void
    {
        $this->auditService->logAction(
            'attendance_marked',
            'AttendanceRecord',
            $record->id,
            [
                'student_id' => $record->student_id,
                'session_id' => $record->session_id,
                'status' => $record->status,
            ],
            "Attendance marked for student {$record->student_id}: {$record->status}" . ($notes ? " ({$notes})" : '')
        );
    }

    public function logAttendanceUpdated(AttendanceRecord $record, array $changes): void
    {
        $this->auditService->logAction(
            'attendance_updated',
            'AttendanceRecord',
            $record->id,
            $changes,
            "Attendance record updated for student {$record->student_id}"
        );
    }

    public function logAttendanceDeleted(AttendanceRecord $record): void
    {
        $this->auditService->recordDeletedModel($record, 'Attendance record deleted');
    }

    public function logBulkAttendanceMarked(int $sessionId, int $successCount, int $failCount): void
    {
        $this->auditService->logAction(
            'bulk_attendance_marked',
            'AttendanceSession',
            $sessionId,
            [
                'success_count' => $successCount,
                'fail_count' => $failCount,
                'total' => $successCount + $failCount,
            ],
            "Bulk attendance marked: {$successCount} successful, {$failCount} failed"
        );
    }

    public function logJustificationSubmitted(Justification $justification): void
    {
        $this->auditService->logAction(
            'justification_submitted',
            'Justification',
            $justification->id,
            [
                'student_id' => $justification->student_id,
                'absence_date' => $justification->absence_date,
                'reason' => $justification->reason,
            ],
            "Justification submitted by student {$justification->student_id}"
        );
    }

    public function logJustificationApproved(Justification $justification): void
    {
        $this->auditService->logAction(
            'justification_approved',
            'Justification',
            $justification->id,
            [
                'student_id' => $justification->student_id,
                'status' => $justification->status,
            ],
            "Justification approved for student {$justification->student_id}"
        );
    }

    public function logJustificationRejected(Justification $justification, ?string $reason = null): void
    {
        $this->auditService->logAction(
            'justification_rejected',
            'Justification',
            $justification->id,
            [
                'student_id' => $justification->student_id,
                'rejection_reason' => $reason,
            ],
            "Justification rejected for student {$justification->student_id}" . ($reason ? ": {$reason}" : '')
        );
    }

    public function logJustificationDeleted(Justification $justification): void
    {
        $this->auditService->recordDeletedModel($justification, 'Justification deleted');
    }

    public function logAbsenceAlertCreated(AbsenceAlert $alert): void
    {
        $this->auditService->logAction(
            'absence_alert_created',
            'AbsenceAlert',
            $alert->id,
            [
                'student_id' => $alert->student_id,
                'absence_threshold' => $alert->absence_threshold,
                'threshold_type' => $alert->threshold_type,
            ],
            "Absence alert created for student {$alert->student_id}"
        );
    }

    public function logAbsenceAlertAcknowledged(AbsenceAlert $alert): void
    {
        $this->auditService->logAction(
            'absence_alert_acknowledged',
            'AbsenceAlert',
            $alert->id,
            [
                'student_id' => $alert->student_id,
                'acknowledged_at' => now(),
            ],
            "Absence alert acknowledged by student {$alert->student_id}"
        );
    }

    public function logAbsenceAlertResolved(AbsenceAlert $alert): void
    {
        $this->auditService->logAction(
            'absence_alert_resolved',
            'AbsenceAlert',
            $alert->id,
            [
                'student_id' => $alert->student_id,
                'resolved_at' => now(),
            ],
            "Absence alert resolved for student {$alert->student_id}"
        );
    }

    public function logRateLimitExceeded(string $ipAddress, string $endpoint): void
    {
        $this->auditService->logAction(
            'rate_limit_exceeded',
            'Attendance',
            null,
            [
                'ip_address' => $ipAddress,
                'endpoint' => $endpoint,
            ],
            "Rate limit exceeded for IP {$ipAddress} on endpoint {$endpoint}"
        );
    }

    public function logSuspiciousActivity(string $ipAddress, string $activityType): void
    {
        $this->auditService->logAction(
            'suspicious_activity_detected',
            'Attendance',
            null,
            [
                'ip_address' => $ipAddress,
                'activity_type' => $activityType,
            ],
            "Suspicious activity detected: {$activityType} from IP {$ipAddress}",
        );
    }

    public function logPermissionDenied(string $action, string $entityType, ?int $entityId = null): void
    {
        $this->auditService->logPermissionDenied($action, $entityType, $entityId);
    }
}
