<?php

namespace Modules\Grades\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAppeal;
use Modules\Grades\Models\Subject;
use Modules\Grades\Services\GradesAuditService;
use Modules\Audit\Models\AuditLog;
use Modules\Students\Models\Student;

class GradesAuditLoggingTest extends TestCase
{
    protected GradesAuditService $auditService;
    protected User $teacher;
    protected Student $student;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auditService = app(GradesAuditService::class);

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');

        $this->student = Student::factory()->create();

        $this->subject = Subject::factory()->create();

        AuditLog::truncate();
    }

    public function test_logs_grade_creation()
    {
        $this->actingAs($this->teacher);

        $grade = Grade::factory()->create([
            'teacher_id' => $this->teacher->id,
        ]);

        $this->auditService->logGradeCreated($grade, 'Test submission');

        $log = AuditLog::where('action', 'grade_created')
            ->where('entity_id', $grade->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Grade', $log->entity_type);
        $this->assertEquals($this->teacher->id, $log->user_id);
    }

    public function test_logs_grade_update()
    {
        $this->actingAs($this->teacher);

        $grade = Grade::factory()->create([
            'teacher_id' => $this->teacher->id,
        ]);

        $changes = ['score' => 90.5];
        $this->auditService->logGradeUpdated($grade, $changes);

        $log = AuditLog::where('action', 'grade_updated')
            ->where('entity_id', $grade->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($changes, $log->changes);
    }

    public function test_logs_grade_deletion()
    {
        $this->actingAs($this->teacher);

        $grade = Grade::factory()->create();
        $this->auditService->logGradeDeleted($grade);

        $log = AuditLog::where('entity_id', $grade->id)->first();

        $this->assertNotNull($log);
    }

    public function test_logs_bulk_grades_import()
    {
        $this->actingAs($this->teacher);

        $this->auditService->logBulkGradesImported(50, 5, 'csv_import');

        $log = AuditLog::where('action', 'grades_bulk_imported')->first();

        $this->assertNotNull($log);
        $this->assertEquals(50, $log->changes['success_count']);
        $this->assertEquals(5, $log->changes['fail_count']);
        $this->assertEquals('csv_import', $log->changes['source']);
    }

    public function test_logs_grade_appeal_submission()
    {
        $this->actingAs($this->teacher);

        $appeal = GradeAppeal::factory()->create();
        $this->auditService->logGradeAppealSubmitted($appeal);

        $log = AuditLog::where('action', 'grade_appeal_submitted')
            ->where('entity_id', $appeal->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('GradeAppeal', $log->entity_type);
    }

    public function test_logs_grade_appeal_approval()
    {
        $this->actingAs($this->teacher);

        $appeal = GradeAppeal::factory()->create([
            'status' => 'approved',
        ]);

        $this->auditService->logGradeAppealApproved($appeal, 'Grade was correct');

        $log = AuditLog::where('action', 'grade_appeal_approved')
            ->where('entity_id', $appeal->id)
            ->first();

        $this->assertNotNull($log);
    }

    public function test_logs_grade_appeal_rejection()
    {
        $this->actingAs($this->teacher);

        $appeal = GradeAppeal::factory()->create([
            'status' => 'rejected',
        ]);

        $this->auditService->logGradeAppealRejected($appeal, 'Grade is accurate');

        $log = AuditLog::where('action', 'grade_appeal_rejected')
            ->where('entity_id', $appeal->id)
            ->first();

        $this->assertNotNull($log);
    }

    public function test_logs_subject_creation()
    {
        $this->actingAs($this->teacher);

        $subject = Subject::factory()->create();
        $this->auditService->logSubjectCreated($subject);

        $log = AuditLog::where('action', 'subject_created')
            ->where('entity_id', $subject->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Subject', $log->entity_type);
    }

    public function test_logs_subject_update()
    {
        $this->actingAs($this->teacher);

        $subject = Subject::factory()->create();
        $changes = ['name' => 'Updated Subject Name'];
        $this->auditService->logSubjectUpdated($subject, $changes);

        $log = AuditLog::where('action', 'subject_updated')
            ->where('entity_id', $subject->id)
            ->first();

        $this->assertNotNull($log);
    }

    public function test_logs_subject_deletion()
    {
        $this->actingAs($this->teacher);

        $subject = Subject::factory()->create();
        $this->auditService->logSubjectDeleted($subject);

        $log = AuditLog::where('entity_id', $subject->id)->first();

        $this->assertNotNull($log);
    }

    public function test_logs_grades_export()
    {
        $this->actingAs($this->teacher);

        $this->auditService->logGradeExported('csv', 150);

        $log = AuditLog::where('action', 'grades_exported')->first();

        $this->assertNotNull($log);
        $this->assertEquals('csv', $log->changes['format']);
        $this->assertEquals(150, $log->changes['record_count']);
    }

    public function test_audit_log_includes_user_context()
    {
        $this->actingAs($this->teacher);

        $grade = Grade::factory()->create();
        $this->auditService->logGradeCreated($grade);

        $log = AuditLog::where('entity_id', $grade->id)->first();

        $this->assertEquals($this->teacher->id, $log->user_id);
        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }

    public function test_audit_log_includes_request_context()
    {
        $this->actingAs($this->teacher);

        $grade = Grade::factory()->create();
        $this->auditService->logGradeCreated($grade);

        $log = AuditLog::where('entity_id', $grade->id)->first();

        $this->assertNotNull($log->url);
        $this->assertNotNull($log->method);
    }

    public function test_permission_denied_logging()
    {
        $this->actingAs($this->teacher);

        $this->auditService->logPermissionDenied('grades.create', 'Grade', 1);

        $log = AuditLog::where('action', 'permission_denied')->first();

        $this->assertNotNull($log);
    }
}
