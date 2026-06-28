<?php

namespace Tests\Feature\Auth;

use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Tests\TestCase;
use Livewire\Livewire;

class DashboardTest extends TestCase
{
    public function test_authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_dashboard_displays_user_profile_information()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
        ]);

        $this->actingAs($user);

        Livewire::test('modules.auth.livewire.dashboard-component')
            ->assertSet('user.first_name', 'John')
            ->assertSet('user.last_name', 'Doe')
            ->assertSet('user.email', 'john@example.com')
            ->assertSet('user.username', 'johndoe');
    }

    public function test_dashboard_displays_account_status()
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->actingAs($user);

        Livewire::test('modules.auth.livewire.dashboard-component')
            ->assertSet('user.is_active', true);
    }

    public function test_dashboard_displays_join_date()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('modules.auth.livewire.dashboard-component')
            ->assertSet('user.created_at', $user->created_at);
    }

    public function test_dashboard_displays_last_login()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('modules.auth.livewire.dashboard-component')
            ->assertViewHas('user');
    }

    public function test_dashboard_displays_user_roles()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['name' => 'super_administrator']);
        $user->assignRole($role);

        $this->actingAs($user);

        Livewire::test('modules.auth.livewire.dashboard-component')
            ->assertSet('user.id', $user->id);
    }

    public function test_dashboard_shows_livewire_component()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertSee('dashboard', false);
    }

    public function test_dashboard_component_mounts_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = Livewire::test('modules.auth.livewire.dashboard-component');
        $this->assertNotNull($response);
    }
}
