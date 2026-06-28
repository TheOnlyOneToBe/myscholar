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
        // User without role should be redirected to admin dashboard
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_dashboard_redirects_student_to_student_dashboard()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
        ]);
        $role = Role::firstOrCreate(['name' => 'student'], ['name' => 'student', 'description' => 'Élève']);
        $user->assignRole($role);

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertRedirect(route('student.dashboard'));
    }

    public function test_dashboard_redirects_parent_to_parent_dashboard()
    {
        $user = User::factory()->create(['is_active' => true]);
        $role = Role::firstOrCreate(['name' => 'parent'], ['name' => 'parent', 'description' => 'Parent']);
        $user->assignRole($role);

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertRedirect(route('parent.dashboard'));
    }

    public function test_dashboard_redirects_admin_to_admin_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'super_administrator'], ['name' => 'super_administrator', 'description' => 'Admin']);
        $user->assignRole($role);

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_redirects_teacher_to_admin_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'enseignant'], ['name' => 'enseignant', 'description' => 'Enseignant']);
        $user->assignRole($role);

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_unauthenticated_dashboard_redirects_to_login()
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }
}
