<?php

namespace Modules\Classes\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Modules\Config\Models\SchoolYear;
use Modules\Classes\Models\ClassModel;
use Modules\Classes\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClassesApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected SchoolYear $activeYear;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $adminRole = Role::create([
            'name' => 'super_administrator',
            'label' => 'Admin',
            'hierarchy_level' => 0,
            'category' => 'super_administrator',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create();
        $this->admin->roles()->sync([$adminRole->id]);

        // Create school year
        $this->activeYear = SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        // Create room
        $this->room = Room::create([
            'name' => 'A101',
            'building' => 'A',
            'capacity' => 45,
            'type' => 'classroom',
        ]);
    }

    /** @test */
    public function test_list_classes()
    {
        ClassModel::create([
            'name' => 'Terminale A1',
            'code' => 'TERM-A1',
            'level' => 'Form 5',
            'section' => 'A',
            'filiere' => 'Science',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin)
            ->getJson('/api/classes')
            ->assertOk()
            ->assertJsonStructure(['data', 'pagination']);
    }

    /** @test */
    public function test_create_class()
    {
        $this->actingAs($this->admin)
            ->postJson('/api/classes', [
                'name' => 'Première S1',
                'code' => 'FIRST-S1',
                'level' => 'Form 4',
                'section' => 'S',
                'filiere' => 'Science',
                'room_id' => $this->room->id,
                'capacity' => 40,
                'school_year_id' => $this->activeYear->id,
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Première S1');

        $this->assertDatabaseHas('classes', [
            'code' => 'FIRST-S1',
        ]);
    }

    /** @test */
    public function test_get_class()
    {
        $class = ClassModel::create([
            'name' => 'Seconde A1',
            'code' => 'SEC-A1',
            'level' => 'Form 3',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin)
            ->getJson("/api/classes/{$class->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $class->id);
    }

    /** @test */
    public function test_update_class()
    {
        $class = ClassModel::create([
            'name' => 'Terminale A1',
            'code' => 'TERM-A1',
            'level' => 'Form 5',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin)
            ->putJson("/api/classes/{$class->id}", [
                'capacity' => 50,
            ])
            ->assertOk()
            ->assertJsonPath('data.capacity', 50);
    }

    /** @test */
    public function test_delete_class()
    {
        $class = ClassModel::create([
            'name' => 'Terminale A1',
            'code' => 'TERM-A1',
            'level' => 'Form 5',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin)
            ->deleteJson("/api/classes/{$class->id}")
            ->assertOk();

        $this->assertDatabaseMissing('classes', ['id' => $class->id]);
    }

    /** @test */
    public function test_list_rooms()
    {
        $this->actingAs($this->admin)
            ->getJson('/api/rooms')
            ->assertOk()
            ->assertJsonStructure(['data', 'pagination']);
    }

    /** @test */
    public function test_create_room()
    {
        $this->actingAs($this->admin)
            ->postJson('/api/rooms', [
                'name' => 'B102',
                'building' => 'B',
                'capacity' => 50,
                'type' => 'classroom',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'B102');
    }

    /** @test */
    public function test_filter_classes_by_level()
    {
        ClassModel::create([
            'name' => 'Terminale A1',
            'code' => 'TERM-A1',
            'level' => 'Form 5',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        ClassModel::create([
            'name' => 'Première A1',
            'code' => 'FIRST-A1',
            'level' => 'Form 4',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 40,
        ]);

        $this->actingAs($this->admin)
            ->getJson('/api/classes?level=Form 5')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
