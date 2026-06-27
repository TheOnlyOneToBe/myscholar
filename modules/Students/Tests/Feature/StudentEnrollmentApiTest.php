<?php

namespace Modules\Students\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentEnrollment;
use Modules\Config\Models\SchoolYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentEnrollmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected SchoolYear $activeYear;
    protected SchoolYear $pastYear;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $enrollmentsViewPerm = Permission::create([
            'permission_id' => 'enrollments.view',
            'name' => 'View Enrollments',
            'module' => 'Students',
            'category' => 'read',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $enrollmentsCreatePerm = Permission::create([
            'permission_id' => 'enrollments.create',
            'name' => 'Create Enrollment',
            'module' => 'Students',
            'category' => 'create',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $enrollmentsEditPerm = Permission::create([
            'permission_id' => 'enrollments.edit',
            'name' => 'Edit Enrollment',
            'module' => 'Students',
            'category' => 'update',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $enrollmentsDeletePerm = Permission::create([
            'permission_id' => 'enrollments.delete',
            'name' => 'Delete Enrollment',
            'module' => 'Students',
            'category' => 'delete',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $enrollmentsManageStatusPerm = Permission::create([
            'permission_id' => 'enrollments.manage_status',
            'name' => 'Manage Enrollment Status',
            'module' => 'Students',
            'category' => 'update',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $enrollOtherYearsPerm = Permission::create([
            'permission_id' => 'students.enroll_other_years',
            'name' => 'Enroll in Other Years',
            'module' => 'Students',
            'category' => 'enroll',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $enrollmentsExportPerm = Permission::create([
            'permission_id' => 'enrollments.export',
            'name' => 'Export Enrollments',
            'module' => 'Students',
            'category' => 'export',
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

        $teacherRole = Role::create([
            'name' => 'enseignant',
            'label' => 'Teacher',
            'hierarchy_level' => 4,
            'category' => 'staff',
            'is_active' => true,
        ]);

        $adminRole->permissions()->attach([
            $enrollmentsViewPerm->id,
            $enrollmentsCreatePerm->id,
            $enrollmentsEditPerm->id,
            $enrollmentsDeletePerm->id,
            $enrollmentsManageStatusPerm->id,
            $enrollOtherYearsPerm->id,
            $enrollmentsExportPerm->id,
        ]);

        $teacherRole->permissions()->attach([
            $enrollmentsViewPerm->id,
            $enrollmentsCreatePerm->id,
            $enrollmentsEditPerm->id,
        ]);

        $this->admin = User::factory()->create();
        $this->admin->roles()->sync([$adminRole->id]);

        $this->teacher = User::factory()->create();
        $this->teacher->roles()->sync([$teacherRole->id]);

        $this->activeYear = SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $this->pastYear = SchoolYear::create([
            'name' => '2023-2024',
            'start_year' => 2023,
            'end_year' => 2024,
            'start_date' => '2023-09-01',
            'end_date' => '2024-06-30',
            'is_active' => false,
        ]);

        $this->student = Student::create([
            'student_id_number' => 'TEST-2024-001',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'date_of_birth' => '2008-05-15',
            'sex' => 'M',
            'email' => 'jean@example.com',
            'phone_number' => '+237691234567',
        ]);
    }

    /** @test */
    public function test_list_enrollments_requires_auth()
    {
        $this->getJson('/api/enrollments')
            ->assertUnauthorized();
    }

    /** @test */
    public function test_list_enrollments_requires_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/enrollments')
            ->assertForbidden();
    }

    /** @test */
    public function test_list_all_enrollments()
    {
        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->getJson('/api/enrollments')
            ->assertOk()
            ->assertJsonStructure(['data', 'pagination']);
    }

    /** @test */
    public function test_filter_enrollments_by_status()
    {
        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->getJson('/api/enrollments?status=active')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function test_filter_enrollments_by_school_year()
    {
        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->getJson('/api/enrollments?school_year_id=' . $this->activeYear->id)
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function test_search_enrollments_by_student_name()
    {
        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->getJson('/api/enrollments?search=Jean')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function test_search_enrollments_by_student_id()
    {
        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->getJson('/api/enrollments?search=TEST-2024-001')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function test_get_single_enrollment()
    {
        $enrollment = StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->getJson("/api/enrollments/{$enrollment->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $enrollment->id);
    }

    /** @test */
    public function test_create_enrollment_requires_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/enrollments', [
                'student_id' => $this->student->id,
                'school_year_id' => $this->activeYear->id,
                'enrollment_date' => now()->toDateString(),
            ])
            ->assertForbidden();
    }

    /** @test */
    public function test_create_enrollment_with_valid_data()
    {
        $this->actingAs($this->admin)
            ->postJson('/api/enrollments', [
                'student_id' => $this->student->id,
                'school_year_id' => $this->activeYear->id,
                'filiere' => 'Science',
                'level' => 'Form 4',
                'enrollment_date' => now()->toDateString(),
                'status' => 'active',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => $this->student->id,
            'filiere' => 'Science',
        ]);
    }

    /** @test */
    public function test_create_enrollment_requires_valid_student()
    {
        $this->actingAs($this->admin)
            ->postJson('/api/enrollments', [
                'student_id' => 9999,
                'school_year_id' => $this->activeYear->id,
                'enrollment_date' => now()->toDateString(),
            ])
            ->assertUnprocessable();
    }

    /** @test */
    public function test_update_enrollment()
    {
        $enrollment = StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
            'filiere' => 'Science',
        ]);

        $this->actingAs($this->admin)
            ->putJson("/api/enrollments/{$enrollment->id}", [
                'status' => 'suspended',
                'filiere' => 'Littéraire',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'suspended')
            ->assertJsonPath('data.filiere', 'Littéraire');
    }

    /** @test */
    public function test_update_enrollment_requires_permission()
    {
        $enrollment = StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->putJson("/api/enrollments/{$enrollment->id}", [
                'status' => 'suspended',
            ])
            ->assertForbidden();
    }

    /** @test */
    public function test_delete_enrollment()
    {
        $enrollment = StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->deleteJson("/api/enrollments/{$enrollment->id}")
            ->assertOk();

        $this->assertDatabaseMissing('student_enrollments', [
            'id' => $enrollment->id,
        ]);
    }

    /** @test */
    public function test_delete_enrollment_requires_permission()
    {
        $enrollment = StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($this->teacher)
            ->deleteJson("/api/enrollments/{$enrollment->id}")
            ->assertForbidden();
    }

    /** @test */
    public function test_suspend_enrollment()
    {
        $enrollment = StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->postJson("/api/enrollments/{$enrollment->id}/suspend")
            ->assertOk()
            ->assertJsonPath('data.status', 'suspended');
    }

    /** @test */
    public function test_resume_enrollment()
    {
        $enrollment = StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'suspended',
        ]);

        $this->actingAs($this->admin)
            ->postJson("/api/enrollments/{$enrollment->id}/resume")
            ->assertOk()
            ->assertJsonPath('data.status', 'active');
    }

    /** @test */
    public function test_export_enrollments_requires_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/enrollments/export')
            ->assertForbidden();
    }

    /** @test */
    public function test_export_enrollments()
    {
        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/enrollments/export');

        $response->assertOk()
            ->assertJsonStructure(['csv', 'filename', 'count']);
    }

    /** @test */
    public function test_get_enrollment_statistics()
    {
        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
            'filiere' => 'Science',
        ]);

        $this->actingAs($this->admin)
            ->getJson('/api/enrollments/statistics')
            ->assertOk()
            ->assertJsonStructure([
                'total_enrollments',
                'active',
                'suspended',
                'by_filiere',
                'by_class',
            ]);
    }

    /** @test */
    public function test_pagination_works()
    {
        for ($i = 0; $i < 30; $i++) {
            StudentEnrollment::create([
                'student_id' => $this->student->id,
                'school_year_id' => $this->activeYear->id,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);
        }

        $this->actingAs($this->admin)
            ->getJson('/api/enrollments?per_page=25')
            ->assertOk()
            ->assertJsonCount(25, 'data')
            ->assertJsonPath('pagination.total', 30);
    }

    /** @test */
    public function test_sort_by_enrollment_date()
    {
        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now()->subDays(5),
            'status' => 'active',
        ]);

        StudentEnrollment::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->activeYear->id,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/enrollments?sort_by=enrollment_date&sort_order=asc');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertTrue(
            $data[0]['enrollment_date'] < $data[1]['enrollment_date']
        );
    }
}
