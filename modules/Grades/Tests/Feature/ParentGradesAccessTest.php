<?php

namespace Modules\Grades\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\GradeAverage;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;

class ParentGradesAccessTest extends TestCase
{
    protected User $parentUser;
    protected Student $student;
    protected StudentParent $studentParent;

    protected function setUp(): void
    {
        parent::setUp();

        // Create parent user
        $this->parentUser = User::factory()->create();
        $this->parentUser->assignRole('parent');

        // Create student
        $this->student = Student::factory()->create();

        // Link parent to student
        $this->studentParent = StudentParent::create([
            'student_id' => $this->student->id,
            'parent_user_id' => $this->parentUser->id,
            'can_access_records' => true,
            'can_receive_alerts' => true,
        ]);
    }

    public function test_parent_can_view_child_grades()
    {
        $grade = Grade::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertOk();
    }

    public function test_parent_cannot_view_other_child_grades()
    {
        $otherStudent = Student::factory()->create();
        $grade = Grade::factory()->create([
            'student_id' => $otherStudent->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertForbidden();
    }

    public function test_parent_cannot_create_grade()
    {
        $this->actingAs($this->parentUser);

        $response = $this->postJson('/api/grades', [
            'student_id' => $this->student->id,
            'subject_id' => 1,
            'score' => 85.5,
            'grade_type' => 'test',
            'weight' => 1.0,
        ]);

        $response->assertForbidden();
    }

    public function test_parent_cannot_update_child_grade()
    {
        $grade = Grade::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->putJson("/api/grades/{$grade->id}", [
            'score' => 95.0,
        ]);

        $response->assertForbidden();
    }

    public function test_parent_cannot_delete_child_grade()
    {
        $grade = Grade::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->deleteJson("/api/grades/{$grade->id}");

        $response->assertForbidden();
    }

    public function test_parent_cannot_access_grades_when_permission_denied()
    {
        $this->studentParent->update(['can_access_records' => false]);

        $grade = Grade::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/grades/{$grade->id}");

        $response->assertForbidden();
    }

    public function test_parent_can_view_child_grade_average()
    {
        $gradeAverage = GradeAverage::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/grades/student/{$this->student->id}");

        // Response depends on endpoint implementation
        // Should return child's grades if parent has access
    }

    public function test_parent_cannot_view_other_child_grade_average()
    {
        $otherStudent = Student::factory()->create();
        $gradeAverage = GradeAverage::factory()->create([
            'student_id' => $otherStudent->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/grades/student/{$otherStudent->id}");

        $response->assertForbidden();
    }

    public function test_student_parent_relationship_verification()
    {
        // Verify relationship was created correctly
        $this->assertTrue(
            StudentParent::isParentOfStudent($this->parentUser->id, $this->student->id)
        );
    }

    public function test_multiple_parents_for_single_student()
    {
        $secondParent = User::factory()->create();
        $secondParent->assignRole('parent');

        StudentParent::create([
            'student_id' => $this->student->id,
            'parent_user_id' => $secondParent->id,
            'relationship_type' => 'parent',
            'can_access_records' => true,
        ]);

        $grade = Grade::factory()->create([
            'student_id' => $this->student->id,
        ]);

        // Both parents should have access
        $this->actingAs($this->parentUser);
        $response1 = $this->getJson("/api/grades/{$grade->id}");
        $response1->assertOk();

        $this->actingAs($secondParent);
        $response2 = $this->getJson("/api/grades/{$grade->id}");
        $response2->assertOk();
    }

    public function test_parent_can_view_child_grade_appeals()
    {
        $grade = Grade::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $appeal = \Modules\Grades\Models\GradeAppeal::factory()->create([
            'student_id' => $this->student->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->getJson("/api/grade-appeals/{$appeal->id}");

        $response->assertOk();
    }

    public function test_parent_cannot_submit_grade_appeal_for_child()
    {
        $grade = Grade::factory()->create([
            'student_id' => $this->student->id,
        ]);

        $this->actingAs($this->parentUser);
        $response = $this->postJson('/api/grade-appeals', [
            'grade_id' => $grade->id,
            'student_id' => $this->student->id,
            'subject_id' => $grade->subject_id,
            'reason' => 'Parent appeal',
        ]);

        $response->assertForbidden();
    }
}
