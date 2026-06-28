<?php

namespace Modules\Grades\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Modules\Students\Models\Student;
use Modules\Config\Models\SchoolYear;
use Modules\Grades\Models\Subject;
use Modules\Grades\Models\GradePeriod;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAppeal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradeAppealTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected SchoolYear $activeYear;
    protected Subject $subject;
    protected GradePeriod $gradePeriod;
    protected Grade $grade;

    protected function setUp(): void
    {
        parent::setUp();

        $viewPerm = Permission::create([
            'permission_id' => 'grade_appeals.view',
            'name' => 'View Grade Appeals',
            'module' => 'Grades',
            'category' => 'read',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $submitPerm = Permission::create([
            'permission_id' => 'grade_appeals.submit',
            'name' => 'Submit Grade Appeal',
            'module' => 'Grades',
            'category' => 'create',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $reviewPerm = Permission::create([
            'permission_id' => 'grade_appeals.review',
            'name' => 'Review Grade Appeals',
            'module' => 'Grades',
            'category' => 'update',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $adminRole = Role::create([
            'name' => 'super_administrator',
            'label' => 'Administrateur Système',
            'hierarchy_level' => 0,
            'category' => 'super_administrator',
            'is_active' => true,
        ]);

        $adminRole->permissions()->attach([$viewPerm->id, $submitPerm->id, $reviewPerm->id]);

        $this->admin = User::factory()->create();
        $this->admin->roles()->sync([$adminRole->id]);

        $this->student = Student::create([
            'student_id_number' => 'STU-2024-00001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '2008-05-15',
            'sex' => 'M',
            'email' => 'john@example.com',
            'phone_number' => '+237691234567',
        ]);

        $this->activeYear = SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'code' => 'MATH',
            'name' => 'Mathematics',
            'is_active' => true,
        ]);

        $this->gradePeriod = GradePeriod::create([
            'school_year_id' => $this->activeYear->id,
            'name' => 'Trimestre 1',
            'start_date' => '2024-09-01',
            'end_date' => '2024-11-30',
            'is_active' => true,
        ]);

        $this->grade = Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->admin->id,
            'score' => 8,
            'grade_type' => 'exam',
        ]);
    }

    public function test_student_can_submit_grade_appeal()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/grade-appeals', [
                'grade_id' => $this->grade->id,
                'subject_id' => $this->subject->id,
                'reason' => 'I believe my grade was calculated incorrectly. I provided answers that should have received more points.',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('grade_appeals', [
            'grade_id' => $this->grade->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_list_grade_appeals()
    {
        GradeAppeal::create([
            'student_id' => $this->student->id,
            'grade_id' => $this->grade->id,
            'subject_id' => $this->subject->id,
            'reason' => 'Grade appeal reason',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/grade-appeals');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_filter_appeals_by_status()
    {
        GradeAppeal::create([
            'student_id' => $this->student->id,
            'grade_id' => $this->grade->id,
            'subject_id' => $this->subject->id,
            'reason' => 'Grade appeal reason',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/grade-appeals?status=pending');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_admin_can_approve_appeal()
    {
        $appeal = GradeAppeal::create([
            'student_id' => $this->student->id,
            'grade_id' => $this->grade->id,
            'subject_id' => $this->subject->id,
            'reason' => 'Grade appeal reason',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/grade-appeals/{$appeal->id}/approve", [
                'response' => 'Your appeal has been reviewed and approved. Grade has been updated.',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('grade_appeals', [
            'id' => $appeal->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_reject_appeal()
    {
        $appeal = GradeAppeal::create([
            'student_id' => $this->student->id,
            'grade_id' => $this->grade->id,
            'subject_id' => $this->subject->id,
            'reason' => 'Grade appeal reason',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/grade-appeals/{$appeal->id}/reject", [
                'response' => 'Your appeal has been reviewed. The grade stands as originally given.',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('grade_appeals', [
            'id' => $appeal->id,
            'status' => 'rejected',
        ]);
    }

    public function test_cannot_submit_appeal_with_short_reason()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/grade-appeals', [
                'grade_id' => $this->grade->id,
                'subject_id' => $this->subject->id,
                'reason' => 'Short',
            ]);

        $response->assertStatus(422);
    }

    public function test_student_can_view_own_appeals()
    {
        GradeAppeal::create([
            'student_id' => $this->student->id,
            'grade_id' => $this->grade->id,
            'subject_id' => $this->subject->id,
            'reason' => 'Grade appeal reason',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/grade-appeals/my');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }
}
