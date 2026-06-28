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
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradeApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected SchoolYear $activeYear;
    protected Subject $subject;
    protected GradePeriod $gradePeriod;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $viewPerm = Permission::create([
            'permission_id' => 'grades.view',
            'name' => 'View Grades',
            'module' => 'Grades',
            'category' => 'read',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $createPerm = Permission::create([
            'permission_id' => 'grades.create',
            'name' => 'Create Grades',
            'module' => 'Grades',
            'category' => 'create',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $editPerm = Permission::create([
            'permission_id' => 'grades.edit',
            'name' => 'Edit Grades',
            'module' => 'Grades',
            'category' => 'update',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $adminRole = Role::create([
            'name' => 'super_administrator',
            'label' => 'Admin',
            'hierarchy_level' => 0,
            'category' => 'admin',
            'is_active' => true,
        ]);

        $adminRole->permissions()->attach([$viewPerm->id, $createPerm->id, $editPerm->id]);

        $this->admin = User::factory()->create();
        $this->admin->roles()->sync([$adminRole->id]);

        $this->teacher = User::factory()->create();
        $this->teacher->roles()->sync([$adminRole->id]);

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
            'credits' => 4,
            'coefficient' => 2.0,
            'is_active' => true,
        ]);

        $this->gradePeriod = GradePeriod::create([
            'school_year_id' => $this->activeYear->id,
            'name' => 'Trimestre 1',
            'start_date' => '2024-09-01',
            'end_date' => '2024-11-30',
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'student_id_number' => 'STU-2024-00001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '2008-05-15',
            'sex' => 'M',
            'email' => 'john@example.com',
            'phone_number' => '+237691234567',
        ]);
    }

    public function test_can_create_grade()
    {
        $response = $this->actingAs($this->teacher)
            ->postJson('/api/grades', [
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'grade_period_id' => $this->gradePeriod->id,
                'school_year_id' => $this->activeYear->id,
                'teacher_id' => $this->teacher->id,
                'score' => 15.5,
                'grade_type' => 'exam',
                'weight' => 2,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('grades', [
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 15.5,
        ]);
    }

    public function test_cannot_create_grade_with_invalid_score()
    {
        $response = $this->actingAs($this->teacher)
            ->postJson('/api/grades', [
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'grade_period_id' => $this->gradePeriod->id,
                'school_year_id' => $this->activeYear->id,
                'teacher_id' => $this->teacher->id,
                'score' => 25,
                'grade_type' => 'exam',
            ]);

        $response->assertStatus(422);
    }

    public function test_can_list_grades()
    {
        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 16,
            'grade_type' => 'exam',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/grades');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_filter_grades_by_subject()
    {
        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 16,
            'grade_type' => 'exam',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/grades?subject_id={$this->subject->id}");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_get_student_grades()
    {
        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 16,
            'grade_type' => 'exam',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/grades/student/{$this->student->id}");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_update_grade()
    {
        $grade = Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 16,
            'grade_type' => 'exam',
        ]);

        $response = $this->actingAs($this->teacher)
            ->putJson("/api/grades/{$grade->id}", [
                'score' => 18,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('grades', [
            'id' => $grade->id,
            'score' => 18,
        ]);
    }

    public function test_can_delete_grade()
    {
        $grade = Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'grade_period_id' => $this->gradePeriod->id,
            'school_year_id' => $this->activeYear->id,
            'teacher_id' => $this->teacher->id,
            'score' => 16,
            'grade_type' => 'exam',
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/grades/{$grade->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('grades', ['id' => $grade->id]);
    }

    public function test_pagination_per_page_parameter()
    {
        for ($i = 1; $i <= 30; $i++) {
            Grade::create([
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'grade_period_id' => $this->gradePeriod->id,
                'school_year_id' => $this->activeYear->id,
                'teacher_id' => $this->teacher->id,
                'score' => 10 + ($i % 10),
                'grade_type' => 'exam',
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->getJson('/api/grades?per_page=10');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
        $this->assertEquals(10, $response->json('pagination.per_page'));
    }
}
