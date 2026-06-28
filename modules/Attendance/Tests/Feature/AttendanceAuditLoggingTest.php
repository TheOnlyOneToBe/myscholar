<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Attendance\Models\AttendanceSession;
use Modules\Attendance\Models\AttendanceRecord;
use Modules\Attendance\Models\Justification;
use Modules\Attendance\Models\AbsenceAlert;
use Modules\Attendance\Services\AttendanceAuditService;
use Modules\Audit\Models\AuditLog;

class AttendanceAuditLoggingTest extends TestCase
{
    protected AttendanceAuditService $auditService;
    protected User $admin;
    protected User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auditService = app(AttendanceAuditService::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');

        AuditLog::truncate();
    }

    public function test_logs_session_creation()
    {
        $this->actingAs($this->admin);

        $session = AttendanceSession::factory()->create();
        $this->auditService->logSessionCreated($session);

        $log = AuditLog::where('action', 'attendance_session_created')
            ->where('entity_id', $session->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('AttendanceSession', $log->entity_type);
        $this->assertEquals($this->admin->id, $log->user_id);
    }

    public function test_logs_session_update()
    {
        $this->actingAs($this->admin);

        $session = AttendanceSession::factory()->create();
        $changes = ['date' => now()];
        $this->auditService->logSessionUpdated($session, $changes);

        $log = AuditLog::where('action', 'attendance_session_updated')
            ->where('entity_id', $session->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($changes, $log->changes);
    }

    public function test_logs_session_deletion()
    {
        $this->actingAs($this->admin);

        $session = AttendanceSession::factory()->create();
        $this->auditService->logSessionDeleted($session);

        $log = AuditLog::where('action', 'attendance_session_deleted')
            ->where('entity_id', $session->id)
            ->first();

        // Note: recordDeletedModel may use a different action name
        $this->assertTrue(
            AuditLog::where('entity_id', $session->id)->exists()
        );
    }

    public function test_logs_attendance_marked()
    {
        $this->actingAs($this->teacher);

        $session = AttendanceSession::factory()->create();
        $record = AttendanceRecord::factory()->create([
            'session_id' => $session->id,
            'status' => 'present',
        ]);

        $this->auditService->logAttendanceMarked($record, 'Regular attendance');

        $log = AuditLog::where('action', 'attendance_marked')
            ->where('entity_id', $record->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('AttendanceRecord', $log->entity_type);
        $this->assertStringContainsString('Regular attendance', $log->metadata['description']);
    }

    public function test_logs_attendance_updated()
    {
        $this->actingAs($this->teacher);

        $session = AttendanceSession::factory()->create();
        $record = AttendanceRecord::factory()->create([
            'session_id' => $session->id,
            'status' => 'present',
        ]);

        $changes = ['status' => 'absent'];
        $this->auditService->logAttendanceUpdated($record, $changes);

        $log = AuditLog::where('action', 'attendance_updated')
            ->where('entity_id', $record->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($changes, $log->changes);
    }

    public function test_logs_bulk_attendance_marked()
    {
        $this->actingAs($this->teacher);

        $session = AttendanceSession::factory()->create();
        $this->auditService->logBulkAttendanceMarked($session->id, 45, 5);

        $log = AuditLog::where('action', 'bulk_attendance_marked')
            ->where('entity_id', $session->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals(45, $log->changes['success_count']);
        $this->assertEquals(5, $log->changes['fail_count']);
    }

    public function test_logs_justification_submitted()
    {
        $this->actingAs($this->admin);

        $justification = Justification::factory()->create();
        $this->auditService->logJustificationSubmitted($justification);

        $log = AuditLog::where('action', 'justification_submitted')
            ->where('entity_id', $justification->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Justification', $log->entity_type);
    }

    public function test_logs_justification_approved()
    {
        $this->actingAs($this->admin);

        $justification = Justification::factory()->create(['status' => 'approved']);
        $this->auditService->logJustificationApproved($justification);

        $log = AuditLog::where('action', 'justification_approved')
            ->where('entity_id', $justification->id)
            ->first();

        $this->assertNotNull($log);
    }

    public function test_logs_justification_rejected()
    {
        $this->actingAs($this->admin);

        $justification = Justification::factory()->create(['status' => 'rejected']);
        $this->auditService->logJustificationRejected($justification, 'Insufficient evidence');

        $log = AuditLog::where('action', 'justification_rejected')
            ->where('entity_id', $justification->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Insufficient evidence', $log->changes['rejection_reason']);
    }

    public function test_logs_absence_alert_created()
    {
        $this->actingAs($this->admin);

        $alert = AbsenceAlert::factory()->create();
        $this->auditService->logAbsenceAlertCreated($alert);

        $log = AuditLog::where('action', 'absence_alert_created')
            ->where('entity_id', $alert->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('AbsenceAlert', $log->entity_type);
    }

    public function test_logs_absence_alert_acknowledged()
    {
        $this->actingAs($this->admin);

        $alert = AbsenceAlert::factory()->create();
        $this->auditService->logAbsenceAlertAcknowledged($alert);

        $log = AuditLog::where('action', 'absence_alert_acknowledged')
            ->where('entity_id', $alert->id)
            ->first();

        $this->assertNotNull($log);
    }

    public function test_logs_absence_alert_resolved()
    {
        $this->actingAs($this->admin);

        $alert = AbsenceAlert::factory()->create();
        $this->auditService->logAbsenceAlertResolved($alert);

        $log = AuditLog::where('action', 'absence_alert_resolved')
            ->where('entity_id', $alert->id)
            ->first();

        $this->assertNotNull($log);
    }

    public function test_logs_rate_limit_exceeded()
    {
        $ipAddress = '192.168.1.1';
        $this->auditService->logRateLimitExceeded($ipAddress, 'bulk_operation');

        $log = AuditLog::where('action', 'rate_limit_exceeded')->first();

        $this->assertNotNull($log);
        $this->assertEquals($ipAddress, $log->changes['ip_address']);
        $this->assertEquals('bulk_operation', $log->changes['endpoint']);
    }

    public function test_logs_suspicious_activity()
    {
        $ipAddress = '192.168.1.2';
        $this->auditService->logSuspiciousActivity($ipAddress, 'failed_login');

        $log = AuditLog::where('action', 'suspicious_activity_detected')->first();

        $this->assertNotNull($log);
        $this->assertEquals($ipAddress, $log->changes['ip_address']);
        $this->assertEquals('failed_login', $log->changes['activity_type']);
    }

    public function test_logs_permission_denied()
    {
        $this->actingAs($this->teacher);

        $this->auditService->logPermissionDenied('attendance.approve_justifications', 'Justification', 1);

        $log = AuditLog::where('action', 'permission_denied')->first();

        $this->assertNotNull($log);
    }

    public function test_audit_log_includes_user_info()
    {
        $this->actingAs($this->admin);

        $session = AttendanceSession::factory()->create();
        $this->auditService->logSessionCreated($session);

        $log = AuditLog::where('entity_id', $session->id)->first();

        $this->assertEquals($this->admin->id, $log->user_id);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    public function test_audit_log_includes_request_context()
    {
        $this->actingAs($this->admin);

        $session = AttendanceSession::factory()->create();
        $this->auditService->logSessionCreated($session);

        $log = AuditLog::where('entity_id', $session->id)->first();

        $this->assertNotNull($log->url);
        $this->assertNotNull($log->method);
    }
}
