<?php

namespace Modules\Students\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Modules\Students\Models\Student;
use Modules\Config\Models\SchoolYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentEnrollmentPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $teacherUser;
    protected User $directorUser;
    protected SchoolYear $activeYear;
    protected SchoolYear $otherYear;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $createStudentPerm = Permission::create([
            'permission_id' => 'students.create',
            'name' => 'Create Student',
            'module' => 'Students',
            'category' => 'create',
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

        // Create roles
        $adminRole = Role::create([
            'name' => 'super_administrator',
            'label' => 'Admin',
            'hierarchy_level' => 0,
            'category' => 'admin',
            'is_active' => true,
        ]);

        $directorRole = Role::create([
            'name' => 'proviseur',
            'label' => 'Director',
            'hierarchy_level' => 1,
            'category' => 'hierarchy',
            'is_active' => true,
        ]);

        $teacherRole = Role::create([
            'name' => 'enseignant',
            'label' => 'Teacher',
            'hierarchy_level' => 4,
            'category' => 'staff',
            'is_active' => true,
        ]);

        // Attach permissions to roles
        $adminRole->permissions()->attach([$createStudentPerm->id, $enrollOtherYearsPerm->id]);
        $directorRole->permissions()->attach([$createStudentPerm->id]);
        $teacherRole->permissions()->attach([$createStudentPerm->id]);

        $this->adminUser = User::factory()->create();
        $this->adminUser->roles()->sync([$adminRole->id]);

        $this->directorUser = User::factory()->create();
        $this->directorUser->roles()->sync([$directorRole->id]);

        $this->teacherUser = User::factory()->create();
        $this->teacherUser->roles()->sync([$teacherRole->id]);

        $this->activeYear = SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $this->otherYear = SchoolYear::create([
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
    public function test_admin_can_enroll_in_active_year()
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/students', [
                'student_id_number' => 'ADM-2024-001',
                'first_name' => 'Marie',
                'last_name' => 'Dupont',
                'date_of_birth' => '2008-05-15',
                'sex' => 'F',
                'email' => 'marie@example.com',
                'phone_number' => '+237691234568',
                'enrollment' => [
                    'school_year_id' => $this->activeYear->id,
                    'filiere' => 'Science',
                ],
            ]);

        $response->assertCreated();
    }

    /** @test */
    public function test_admin_can_enroll_in_other_years()
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/students', [
                'student_id_number' => 'ADM-2023-001',
                'first_name' => 'Pierre',
                'last_name' => 'Martin',
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => 'pierre@example.com',
                'phone_number' => '+237691234569',
                'enrollment' => [
                    'school_year_id' => $this->otherYear->id,
                    'filiere' => 'Littéraire',
                ],
            ]);

        $response->assertCreated();
    }

    /** @test */
    public function test_teacher_can_enroll_in_active_year()
    {
        $response = $this->actingAs($this->teacherUser)
            ->postJson('/api/students', [
                'student_id_number' => 'TEA-2024-001',
                'first_name' => 'Sophie',
                'last_name' => 'Bernard',
                'date_of_birth' => '2008-05-15',
                'sex' => 'F',
                'email' => 'sophie@example.com',
                'phone_number' => '+237691234570',
                'enrollment' => [
                    'school_year_id' => $this->activeYear->id,
                    'filiere' => 'Commercial',
                ],
            ]);

        $response->assertCreated();
    }

    /** @test */
    public function test_teacher_cannot_enroll_in_other_years()
    {
        $response = $this->actingAs($this->teacherUser)
            ->postJson('/api/students', [
                'student_id_number' => 'TEA-2023-001',
                'first_name' => 'Louis',
                'last_name' => 'Dupuis',
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => 'louis@example.com',
                'phone_number' => '+237691234571',
                'enrollment' => [
                    'school_year_id' => $this->otherYear->id,
                    'filiere' => 'Science',
                ],
            ]);

        $response->assertUnprocessable();
    }

    /** @test */
    public function test_director_without_permission_cannot_enroll_in_other_years()
    {
        $response = $this->actingAs($this->directorUser)
            ->postJson('/api/students', [
                'student_id_number' => 'DIR-2023-001',
                'first_name' => 'Claude',
                'last_name' => 'Blanc',
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => 'claude@example.com',
                'phone_number' => '+237691234572',
                'enrollment' => [
                    'school_year_id' => $this->otherYear->id,
                    'filiere' => 'Science',
                ],
            ]);

        $response->assertUnprocessable();
    }

    /** @test */
    public function test_auto_enrollment_without_school_year_id()
    {
        $response = $this->actingAs($this->teacherUser)
            ->postJson('/api/students', [
                'student_id_number' => 'AUTO-2024-001',
                'first_name' => 'Anne',
                'last_name' => 'Laurent',
                'date_of_birth' => '2008-05-15',
                'sex' => 'F',
                'email' => 'anne@example.com',
                'phone_number' => '+237691234573',
                'enrollment' => [
                    'filiere' => 'Science',
                ],
            ]);

        $response->assertCreated();

        // Verify auto-set to active year
        $enrollment = $response->json('data.enrollments.0');
        $this->assertEquals($this->activeYear->id, $enrollment['school_year_id']);
    }

    /** @test */
    public function test_enrollment_blocked_to_active_year_without_permission()
    {
        // Teacher tries to override school year but doesn't have permission
        $response = $this->actingAs($this->teacherUser)
            ->postJson('/api/students', [
                'student_id_number' => 'BLOCK-2024-001',
                'first_name' => 'Nathalie',
                'last_name' => 'Moreau',
                'date_of_birth' => '2008-05-15',
                'sex' => 'F',
                'email' => 'nathalie@example.com',
                'phone_number' => '+237691234574',
                'enrollment' => [
                    'school_year_id' => $this->otherYear->id,
                    'filiere' => 'Littéraire',
                ],
            ]);

        $response->assertUnprocessable();
        $this->assertArrayHasKey('enrollment.school_year_id', $response->json('errors'));
    }

    /** @test */
    public function test_multiple_students_with_permission_check()
    {
        // Admin enrolls student 1 in active year
        $response1 = $this->actingAs($this->adminUser)
            ->postJson('/api/students', [
                'student_id_number' => 'MULTI-2024-001',
                'first_name' => 'Student',
                'last_name' => 'One',
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => 'one@example.com',
                'phone_number' => '+237691234575',
                'enrollment' => [
                    'school_year_id' => $this->activeYear->id,
                ],
            ]);
        $response1->assertCreated();

        // Admin enrolls student 2 in other year
        $response2 = $this->actingAs($this->adminUser)
            ->postJson('/api/students', [
                'student_id_number' => 'MULTI-2023-001',
                'first_name' => 'Student',
                'last_name' => 'Two',
                'date_of_birth' => '2008-05-15',
                'sex' => 'F',
                'email' => 'two@example.com',
                'phone_number' => '+237691234576',
                'enrollment' => [
                    'school_year_id' => $this->otherYear->id,
                ],
            ]);
        $response2->assertCreated();

        // Teacher enrolls student 3 in active year
        $response3 = $this->actingAs($this->teacherUser)
            ->postJson('/api/students', [
                'student_id_number' => 'MULTI-2024-002',
                'first_name' => 'Student',
                'last_name' => 'Three',
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => 'three@example.com',
                'phone_number' => '+237691234577',
                'enrollment' => [
                    'school_year_id' => $this->activeYear->id,
                ],
            ]);
        $response3->assertCreated();

        // Verify all 3 students were created
        $this->assertDatabaseCount('students', 4); // 3 new + 1 from setUp
    }
}
