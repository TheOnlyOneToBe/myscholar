<?php

namespace Tests\Feature\Config;

use Modules\Auth\Models\User;
use Modules\Config\Models\SchoolInfo;
use Modules\Config\Models\SchoolYear;
use Modules\Config\Livewire\DetailComponent;
use Tests\TestCase;
use Livewire\Livewire;

class DetailComponentTest extends TestCase
{
    public function test_detail_component_loads_school_info()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schoolInfo = SchoolInfo::create([
            'name' => 'Lycée Test',
            'acronym' => 'LT',
            'city' => 'Douala',
        ]);

        Livewire::test(DetailComponent::class)
            ->assertSet('schoolInfo.name', 'Lycée Test')
            ->assertSet('schoolInfo.acronym', 'LT');
    }

    public function test_detail_component_loads_current_school_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        Livewire::test(DetailComponent::class)
            ->assertSet('currentSchoolYear.name', '2024-2025')
            ->assertSet('currentSchoolYear.is_active', true);
    }

    public function test_can_toggle_edit_mode()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(DetailComponent::class)
            ->assertSet('editMode', false)
            ->call('toggleEditMode')
            ->assertSet('editMode', true)
            ->call('toggleEditMode')
            ->assertSet('editMode', false);
    }

    public function test_can_update_school_info()
    {
        $admin = User::factory()->create();
        $adminRole = \Modules\Auth\Models\Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        SchoolInfo::create([
            'name' => 'Old Name',
            'school_type' => 'public',
        ]);

        Livewire::test(DetailComponent::class)
            ->call('toggleEditMode')
            ->set('formData.name', 'New Lycée Name')
            ->set('formData.school_type', 'prive')
            ->call('updateSchoolInfo')
            ->assertSet('editMode', false);

        $this->assertDatabaseHas('school_info', ['name' => 'New Lycée Name']);
    }

    public function test_cannot_update_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        SchoolInfo::create([
            'name' => 'Original Name',
            'school_type' => 'public',
        ]);

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        Livewire::test(DetailComponent::class, [])
            ->call('updateSchoolInfo');
    }

    public function test_get_system_setting()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        \Modules\Config\Models\SystemSetting::set('timezone', 'Africa/Douala', 'string', 'general');

        $timezone = \Modules\Config\Models\SystemSetting::get('timezone');
        $this->assertEquals('Africa/Douala', $timezone);
    }
}
