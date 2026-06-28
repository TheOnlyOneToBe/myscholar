<?php

namespace Modules\Classes\Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Permission;
use Modules\Classes\Livewire\ClassListComponent;
use Modules\Classes\Models\ClassModel;
use Modules\Config\Models\SchoolYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClassListComponentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected SchoolYear $activeYear;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permission
        $viewPerm = Permission::create([
            'permission_id' => 'classes.view',
            'name' => 'View Classes',
            'module' => 'Classes',
            'category' => 'read',
            'scope' => 'global',
            'is_active' => true,
        ]);

        // Create role
        $adminRole = Role::create([
            'name' => 'super_administrator',
            'label' => 'Administrateur Système',
            'hierarchy_level' => 0,
            'category' => 'super_administrator',
            'is_active' => true,
        ]);

        $adminRole->permissions()->attach($viewPerm->id);

        // Create user
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
    }

    public function test_component_renders_successfully()
    {
        $this->actingAs($this->admin);

        Livewire::test(ClassListComponent::class)
            ->assertViewHas('classes');
    }

    public function test_component_displays_classes()
    {
        $class = ClassModel::create([
            'name' => 'Form 1A',
            'code' => 'F1A',
            'level' => 'Form 1',
            'filiere' => 'Science',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClassListComponent::class)
            ->assertSee('Form 1A')
            ->assertSee('F1A');
    }

    public function test_component_can_filter_by_level()
    {
        ClassModel::create([
            'name' => 'Form 1A',
            'code' => 'F1A',
            'level' => 'Form 1',
            'filiere' => 'Science',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        ClassModel::create([
            'name' => 'Form 2A',
            'code' => 'F2A',
            'level' => 'Form 2',
            'filiere' => 'Science',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClassListComponent::class)
            ->set('level', 'Form 1')
            ->assertSee('Form 1A');
    }

    public function test_component_can_search_by_name()
    {
        ClassModel::create([
            'name' => 'Form 1A',
            'code' => 'F1A',
            'level' => 'Form 1',
            'filiere' => 'Science',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        ClassModel::create([
            'name' => 'Form 1B',
            'code' => 'F1B',
            'level' => 'Form 1',
            'filiere' => 'Littéraire',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClassListComponent::class)
            ->set('search', 'Form 1A')
            ->assertSee('Form 1A');
    }

    public function test_component_open_and_close_form()
    {
        $this->actingAs($this->admin);

        Livewire::test(ClassListComponent::class)
            ->call('openForm')
            ->assertSet('showForm', true)
            ->call('closeForm')
            ->assertSet('showForm', false);
    }

    public function test_component_can_update_class()
    {
        $class = ClassModel::create([
            'name' => 'Form 1A',
            'code' => 'F1A',
            'level' => 'Form 1',
            'filiere' => 'Science',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClassListComponent::class)
            ->call('openForm', $class->id)
            ->set('form.name', 'Form 1A Updated')
            ->call('saveClass');

        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'name' => 'Form 1A Updated',
        ]);
    }

    public function test_component_can_delete_class()
    {
        $class = ClassModel::create([
            'name' => 'Form 1A',
            'code' => 'F1A',
            'level' => 'Form 1',
            'filiere' => 'Science',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClassListComponent::class)
            ->call('confirmDelete', $class->id)
            ->call('deleteClass');

        $this->assertDatabaseMissing('classes', [
            'id' => $class->id,
        ]);
    }

    public function test_component_can_sort_classes()
    {
        ClassModel::create([
            'name' => 'Zebra Class',
            'code' => 'ZC',
            'level' => 'Form 1',
            'filiere' => 'Science',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        ClassModel::create([
            'name' => 'Apple Class',
            'code' => 'AC',
            'level' => 'Form 1',
            'filiere' => 'Science',
            'school_year_id' => $this->activeYear->id,
            'capacity' => 45,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClassListComponent::class)
            ->assertSet('sort_by', 'name')
            ->assertSet('sort_order', 'asc')
            ->call('toggleSort', 'name')
            ->assertSet('sort_order', 'desc');
    }

    public function test_component_applies_pagination()
    {
        for ($i = 1; $i <= 15; $i++) {
            ClassModel::create([
                'name' => "Form $i",
                'code' => "F$i",
                'level' => "Form $i",
                'filiere' => 'Science',
                'school_year_id' => $this->activeYear->id,
                'capacity' => 45,
            ]);
        }

        $this->actingAs($this->admin);

        Livewire::test(ClassListComponent::class)
            ->set('per_page', 10);
    }
}
