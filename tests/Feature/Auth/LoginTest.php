<?php

namespace Tests\Feature\Auth;

use Modules\Auth\Models\User;
use Tests\TestCase;
use Livewire\Livewire;

class LoginTest extends TestCase
{
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_view_login_form()
    {
        $this->get('/login')
            ->assertSee('login', false);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'test@example.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors('email');
    }

    public function test_login_fails_without_email()
    {
        Livewire::test('modules.auth.livewire.login-component')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');
    }

    public function test_login_fails_without_password()
    {
        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'test@example.com')
            ->call('login')
            ->assertHasErrors('password');
    }

    public function test_login_fails_with_invalid_email_format()
    {
        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'not-an-email')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');
    }

    public function test_authenticated_user_cannot_access_login_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/login');
        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_remember_me_functionality()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('remember', true)
            ->call('login')
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_session_regenerated_after_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $oldSessionId = session()->getId();

        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('login');

        // Session should be regenerated
        $this->assertNotEquals($oldSessionId, session()->getId());
    }

    public function test_inactive_user_cannot_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'is_active' => false,
        ]);

        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');
    }
}
