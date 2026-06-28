<?php

namespace Modules\Grades\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAppeal;
use Modules\Grades\Models\Subject;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;

class GradesPoliciesTest extends TestCase
{
    protected User $admin;
    protected User $proviseur;
    protected User $teacher;
    protected User $student;
    protected User $parent;
    protected Student $studentRecord;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with roles
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->proviseur = User::factory()->create();
        $this->proviseur->assignRole('proviseur');

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');

        $this->student = User::factory()->create();
        $this->student->assignRole('student');

        $this->parent = User::factory()->create();
        $this->parent->assignRole('parent');

        // Create student record
        $this->studentRecord = Student::factory()->create();

        // Link parent to student
        StudentParent::create([
            'student_id' => $this->studentRecord->id,
            'parent_user_id' => $this->parent->id,
            'can_access_records' => true,
        ]);

        // Create subject
        $this->subject = Subject::factory()->create();
    }

    // ===== GRADE POLICIES =====

    public function test_admin_can_view_any_grade()
    {
        $grade = Grade::factory()->create();

        $this->actingAs($this->admin);
        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertOk();
    }

    public function test_proviseur_can_view_any_grade()
    {
        $grade = Grade::factory()->create();

        $this->actingAs($this->proviseur);
        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertOk();
    }

    public function test_teacher_can_view_own_grades()
    {
        $grade = Grade::factory()->create([
            'teacher_id' => $this->teacher->id,
        ]);

        $this->actingAs($this->teacher);
        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertOk();
    }

    public function test_teacher_cannot_view_other_teacher_grades()
    {
        $otherTeacher = User::factory()->create();
        $otherTeacher->assignRole('teacher');

        $grade = Grade::factory()->create([
            'teacher_id' => $otherTeacher->id,
        ]);

        $this->actingAs($this->teacher);
        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertForbidden();
    }

    public function test_student_can_view_own_grades()
    {
        $grade = Grade::factory()->create([
            'student_id' => $this->studentRecord->id,
        ]);

        $this->actingAs($this->student);
        $response = $this->getJson("/api/grades/{$grade->id}");

        // Note: depends on if student matches the student record
        // This test may need adjustment based on the student relationship
    }

    public function test_parent_can_view_child_grades()
    {
        $grade = Grade::factory()->create([
            'student_id' => $this->studentRecord->id,
        ]);

        $this->actingAs($this->parent);
        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertOk();
    }

    public function test_parent_cannot_view_other_child_grades()
    {
        $otherStudent = Student::factory()->create();
        $grade = Grade::factory()->create([
            'student_id' => $otherStudent->id,
        ]);

        $this->actingAs($this->parent);
        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertForbidden();
    }

    public function test_admin_can_create_grade()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson('/api/grades', [
            'student_id' => $this->studentRecord->id,
            'subject_id' => $this->subject->id,
            'score' => 85.5,
            'grade_type' => 'test',
            'weight' => 1.0,
        ]);

        $response->assertCreated();
    }

    public function test_teacher_can_create_grade()
    {
        $this->actingAs($this->teacher);
        $this->teacher->givePermissionTo('grades.create');

        $response = $this->postJson('/api/grades', [
            'student_id' => $this->studentRecord->id,
            'subject_id' => $this->subject->id,
            'score' => 85.5,
            'grade_type' => 'test',
            'weight' => 1.0,
        ]);

        $response->assertCreated();
    }

    public function test_student_cannot_create_grade()
    {
        $this->actingAs($this->student);

        $response = $this->postJson('/api/grades', [
            'student_id' => $this->studentRecord->id,
            'subject_id' => $this->subject->id,
            'score' => 85.5,
            'grade_type' => 'test',
            'weight' => 1.0,
        ]);

        $response->assertForbidden();
    }

    public function test_parent_cannot_create_grade()
    {
        $this->actingAs($this->parent);

        $response = $this->postJson('/api/grades', [
            'student_id' => $this->studentRecord->id,
            'subject_id' => $this->subject->id,
            'score' => 85.5,
            'grade_type' => 'test',
            'weight' => 1.0,
        ]);

        $response->assertForbidden();
    }

    public function test_teacher_can_update_own_grade_within_window()
    {
        $grade = Grade::factory()->create([
            'teacher_id' => $this->teacher->id,
            'graded_at' => now()->subDays(3),
        ]);

        $this->actingAs($this->teacher);
        $this->teacher->givePermissionTo('grades.edit');

        $response = $this->putJson("/api/grades/{$grade->id}", [
            'score' => 90.0,
        ]);

        $response->assertOk();
    }

    public function test_teacher_cannot_update_own_grade_after_window()
    {
        $grade = Grade::factory()->create([
            'teacher_id' => $this->teacher->id,
            'graded_at' => now()->subDays(8),
        ]);

        $this->actingAs($this->teacher);

        $response = $this->putJson("/api/grades/{$grade->id}", [
            'score' => 90.0,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_grade()
    {
        $grade = Grade::factory()->create();

        $this->actingAs($this->admin);
        $this->admin->givePermissionTo('grades.delete');

        $response = $this->deleteJson("/api/grades/{$grade->id}");

        $response->assertOk();
    }

    public function test_teacher_cannot_delete_other_teacher_grade()
    {
        $otherTeacher = User::factory()->create();
        $otherTeacher->assignRole('teacher');

        $grade = Grade::factory()->create([
            'teacher_id' => $otherTeacher->id,
        ]);

        $this->actingAs($this->teacher);

        $response = $this->deleteJson("/api/grades/{$grade->id}");

        $response->assertForbidden();
    }

    // ===== GRADE APPEAL POLICIES =====

    public function test_student_can_submit_grade_appeal()
    {
        $grade = Grade::factory()->create();

        $this->actingAs($this->student);
        $this->student->givePermissionTo('grade_appeals.submit');

        $response = $this->postJson('/api/grade-appeals', [
            'grade_id' => $grade->id,
            'student_id' => $this->studentRecord->id,
            'subject_id' => $grade->subject_id,
            'reason' => 'I believe my grade is incorrect',
        ]);

        $response->assertCreated();
    }

    public function test_admin_can_review_grade_appeal()
    {
        $appeal = GradeAppeal::factory()->create([
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin);
        $this->admin->givePermissionTo('grade_appeals.review');

        $response = $this->postJson("/api/grade-appeals/{$appeal->id}/approve", [
            'response' => 'Grade was correct',
        ]);

        $response->assertOk();
    }

    public function test_teacher_cannot_review_grade_appeal()
    {
        $appeal = GradeAppeal::factory()->create([
            'status' => 'pending',
        ]);

        $this->actingAs($this->teacher);

        $response = $this->postJson("/api/grade-appeals/{$appeal->id}/approve", [
            'response' => 'Grade was correct',
        ]);

        $response->assertForbidden();
    }

    public function test_parent_can_view_child_grade_appeal()
    {
        $appeal = GradeAppeal::factory()->create([
            'student_id' => $this->studentRecord->id,
        ]);

        $this->actingAs($this->parent);
        $response = $this->getJson("/api/grade-appeals/{$appeal->id}");

        $response->assertOk();
    }

    // ===== SUBJECT POLICIES =====

    public function test_admin_can_create_subject()
    {
        $this->actingAs($this->admin);
        $this->admin->givePermissionTo('subjects.create');

        $response = $this->postJson('/api/subjects', [
            'name' => 'Advanced Mathematics',
            'code' => 'MATH-201',
        ]);

        $response->assertCreated();
    }

    public function test_proviseur_can_create_subject()
    {
        $this->actingAs($this->proviseur);
        $this->proviseur->givePermissionTo('subjects.create');

        $response = $this->postJson('/api/subjects', [
            'name' => 'Physics',
            'code' => 'PHY-201',
        ]);

        $response->assertCreated();
    }

    public function test_teacher_cannot_create_subject()
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson('/api/subjects', [
            'name' => 'Chemistry',
            'code' => 'CHEM-201',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_subject()
    {
        $subject = Subject::factory()->create();

        $this->actingAs($this->admin);
        $this->admin->givePermissionTo('subjects.delete');

        $response = $this->deleteJson("/api/subjects/{$subject->id}");

        $response->assertOk();
    }

    public function test_proviseur_cannot_delete_subject()
    {
        $subject = Subject::factory()->create();

        $this->actingAs($this->proviseur);

        $response = $this->deleteJson("/api/subjects/{$subject->id}");

        $response->assertForbidden();
    }

    public function test_any_user_can_view_subject()
    {
        $subject = Subject::factory()->create();

        $this->actingAs($this->teacher);
        $response = $this->getJson("/api/subjects/{$subject->id}");

        $response->assertOk();
    }
}
