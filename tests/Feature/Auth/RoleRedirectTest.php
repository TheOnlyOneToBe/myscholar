<?php

namespace Tests\Feature\Auth;

use Modules\Auth\Models\User;
use Modules\Auth\Models\Role;
use Tests\TestCase;

class RoleRedirectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        $this->ensureRolesExist();
    }

    private function ensureRolesExist(): void
    {
        $roles = [
            'super_administrator' => 'Administrateur Système',
            'proviseur' => 'Proviseur',
            'censeur' => 'Censeur',
            'prof_principal' => 'Professeur Principal',
            'enseignant' => 'Enseignant',
            'student' => 'Élève',
            'parent' => 'Parent',
            'surveillant' => 'Surveillant',
            'secretaire' => 'Secrétaire',
            'comptable' => 'Comptable',
        ];

        foreach ($roles as $name => $label) {
            Role::firstOrCreate(
                ['name' => $name],
                ['name' => $name, 'description' => $label]
            );
        }
    }

    public function test_student_user_redirects_to_student_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'student')->first();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('student.dashboard'));
    }

    public function test_parent_user_redirects_to_parent_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'parent')->first();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('parent.dashboard'));
    }

    public function test_admin_user_redirects_to_admin_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'proviseur')->first();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_teacher_user_redirects_to_admin_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'enseignant')->first();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_censeur_user_redirects_to_admin_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'censeur')->first();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_user_without_role_redirects_to_admin_dashboard()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_student_cannot_access_parent_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'student')->first();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/parent-dashboard')
            ->assertStatus(403);
    }

    public function test_parent_can_access_parent_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'parent')->first();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/parent-dashboard')
            ->assertStatus(200);
    }

    public function test_non_authenticated_user_cannot_access_any_dashboard()
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');

        $this->get('/parent-dashboard')
            ->assertRedirect('/login');

        $this->get('/student-dashboard')
            ->assertRedirect('/login');

        $this->get('/admin-dashboard')
            ->assertRedirect('/login');
    }
}
