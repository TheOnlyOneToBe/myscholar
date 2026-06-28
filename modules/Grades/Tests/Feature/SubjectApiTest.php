<?php

namespace Modules\Grades\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Modules\Grades\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $viewPerm = Permission::create([
            'permission_id' => 'subjects.view',
            'name' => 'View Subjects',
            'module' => 'Grades',
            'category' => 'read',
            'scope' => 'global',
            'is_active' => true,
        ]);

        $createPerm = Permission::create([
            'permission_id' => 'subjects.create',
            'name' => 'Create Subjects',
            'module' => 'Grades',
            'category' => 'create',
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

        $adminRole->permissions()->attach([$viewPerm->id, $createPerm->id]);

        $this->admin = User::factory()->create();
        $this->admin->roles()->sync([$adminRole->id]);
    }

    public function test_can_create_subject()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/subjects', [
                'code' => 'MATH',
                'name' => 'Mathematics',
                'credits' => 4,
                'coefficient' => 2.0,
                'is_active' => true,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('subjects', [
            'code' => 'MATH',
            'name' => 'Mathematics',
        ]);
    }

    public function test_cannot_create_duplicate_subject_code()
    {
        Subject::create([
            'code' => 'MATH',
            'name' => 'Mathematics',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/subjects', [
                'code' => 'MATH',
                'name' => 'Mathematics II',
                'is_active' => true,
            ]);

        $response->assertStatus(422);
    }

    public function test_can_list_subjects()
    {
        Subject::create([
            'code' => 'MATH',
            'name' => 'Mathematics',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/subjects');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'pagination']);
    }

    public function test_can_get_single_subject()
    {
        $subject = Subject::create([
            'code' => 'MATH',
            'name' => 'Mathematics',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/subjects/{$subject->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('code', 'MATH');
    }

    public function test_can_update_subject()
    {
        $subject = Subject::create([
            'code' => 'MATH',
            'name' => 'Mathematics',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/subjects/{$subject->id}", [
                'name' => 'Advanced Mathematics',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Advanced Mathematics',
        ]);
    }

    public function test_can_delete_subject()
    {
        $subject = Subject::create([
            'code' => 'MATH',
            'name' => 'Mathematics',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/subjects/{$subject->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }
}
