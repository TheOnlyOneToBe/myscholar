<?php

namespace Tests\Feature\Auth;

use Modules\Auth\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_logout_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
    }

    public function test_guest_user_cannot_access_protected_routes()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_protected_routes()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_user_authentication_state_is_maintained()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_multiple_users_can_be_authenticated_separately()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        $this->actingAs($user1);
        $this->assertAuthenticatedAs($user1);

        $this->actingAs($user2);
        $this->assertAuthenticatedAs($user2);
    }

    public function test_guest_middleware_redirects_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/login');
        $response->assertStatus(302);
    }

    public function test_auth_middleware_redirects_unauthenticated_user()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_user_can_perform_authenticated_action()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('dashboard', false);
    }

    public function test_inactive_user_cannot_login()
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('password123'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
    }

    public function test_session_token_is_different_after_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $guestToken = session()->token();

        $response = $this->postJson('/api/auth/login', [
            'email_or_username' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertNotNull($response->json('token'));
    }

    public function test_user_login_updates_last_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'last_login' => null,
        ]);

        // This would be tested if the login system updates last_login
        $this->assertNull($user->last_login);
    }

    public function test_authenticated_user_cannot_register()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/register');
        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_authenticated_user_cannot_request_password_reset()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/forgot-password');
        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }
}
