<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Modules\Students\Models\Student;
use Modules\Config\Models\SchoolYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected SchoolYear $activeYear;

    protected function setUp(): void
    {
        parent::setUp();

        $viewPerm = Permission::create([
            'permission_id' => 'students.view',
            'name' => 'View Students',
            'module' => 'Students',
            'category' => 'read',
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

        $adminRole->permissions()->attach($viewPerm->id);

        $this->admin = User::factory()->create();
        $this->admin->roles()->sync([$adminRole->id]);

        $this->activeYear = SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);
    }

    public function test_pagination_info_in_response()
    {
        for ($i = 1; $i <= 5; $i++) {
            Student::create([
                'student_id_number' => sprintf('STU-2024-%05d', $i),
                'first_name' => "Student{$i}",
                'last_name' => "Test",
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => "student{$i}@example.com",
                'phone_number' => "+237691234567",
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->getJson('/api/students');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'pagination' => [
                'total',
                'per_page',
                'current_page',
                'last_page',
                'from',
                'to',
            ],
        ]);
    }

    public function test_pagination_respects_per_page_parameter()
    {
        for ($i = 1; $i <= 15; $i++) {
            Student::create([
                'student_id_number' => sprintf('STU-2024-%05d', $i),
                'first_name' => "Student{$i}",
                'last_name' => "Test",
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => "student{$i}@example.com",
                'phone_number' => "+237691234567",
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->getJson('/api/students?per_page=10');

        $response->assertOk();
        $data = $response->json('pagination');
        $this->assertEquals(10, $data['per_page']);
        $this->assertCount(10, $response->json('data'));
    }

    public function test_pagination_default_per_page()
    {
        for ($i = 1; $i <= 30; $i++) {
            Student::create([
                'student_id_number' => sprintf('STU-2024-%05d', $i),
                'first_name' => "Student{$i}",
                'last_name' => "Test",
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => "student{$i}@example.com",
                'phone_number' => "+237691234567",
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->getJson('/api/students');

        $response->assertOk();
        $data = $response->json('pagination');
        $this->assertEquals(25, $data['per_page']);
        $this->assertCount(25, $response->json('data'));
    }

    public function test_pagination_page_parameter()
    {
        for ($i = 1; $i <= 30; $i++) {
            Student::create([
                'student_id_number' => sprintf('STU-2024-%05d', $i),
                'first_name' => "Student{$i}",
                'last_name' => "Test",
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => "student{$i}@example.com",
                'phone_number' => "+237691234567",
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->getJson('/api/students?page=2&per_page=10');

        $response->assertOk();
        $data = $response->json('pagination');
        $this->assertEquals(2, $data['current_page']);
        $this->assertEquals(10, $data['per_page']);
    }

    public function test_pagination_total_count()
    {
        $count = 23;
        for ($i = 1; $i <= $count; $i++) {
            Student::create([
                'student_id_number' => sprintf('STU-2024-%05d', $i),
                'first_name' => "Student{$i}",
                'last_name' => "Test",
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => "student{$i}@example.com",
                'phone_number' => "+237691234567",
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->getJson('/api/students');

        $response->assertOk();
        $data = $response->json('pagination');
        $this->assertEquals($count, $data['total']);
    }

    public function test_pagination_last_page()
    {
        for ($i = 1; $i <= 50; $i++) {
            Student::create([
                'student_id_number' => sprintf('STU-2024-%05d', $i),
                'first_name' => "Student{$i}",
                'last_name' => "Test",
                'date_of_birth' => '2008-05-15',
                'sex' => 'M',
                'email' => "student{$i}@example.com",
                'phone_number' => "+237691234567",
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->getJson('/api/students?per_page=25');

        $response->assertOk();
        $data = $response->json('pagination');
        $this->assertEquals(2, $data['last_page']);
    }
}
