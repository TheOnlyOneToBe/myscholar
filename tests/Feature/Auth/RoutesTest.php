<?php

namespace Tests\Feature\Auth;

use Modules\Auth\Models\User;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    public function test_login_route_exists()
    {
        $response = $this->get('/login');
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    public function test_register_route_exists()
    {
        $response = $this->get('/register');
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    public function test_forgot_password_route_exists()
    {
        $response = $this->get('/forgot-password');
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    public function test_reset_password_route_exists()
    {
        $token = bin2hex(random_bytes(32));
        $response = $this->get('/reset-password/' . $token);
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    public function test_dashboard_route_exists()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    public function test_logout_route_exists()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    public function test_login_route_uses_guest_middleware()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/login');
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_register_route_uses_guest_middleware()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/register');
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_dashboard_route_uses_auth_middleware()
    {
        $response = $this->get('/dashboard');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->headers->get('Location'));
    }

    public function test_logout_route_uses_auth_middleware()
    {
        $response = $this->post('/logout');
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function test_login_route_returns_200()
    {
        $response = $this->get('/login');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_register_route_returns_200()
    {
        $response = $this->get('/register');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_forgot_password_route_returns_200()
    {
        $response = $this->get('/forgot-password');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_reset_password_route_returns_200()
    {
        $token = bin2hex(random_bytes(32));
        $response = $this->get('/reset-password/' . $token);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_dashboard_route_has_correct_name()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $url = route('dashboard');
        $this->assertStringContainsString('/dashboard', $url);
    }

    public function test_login_route_has_correct_name()
    {
        $url = route('login');
        $this->assertStringContainsString('/login', $url);
    }

    public function test_register_route_has_correct_name()
    {
        $url = route('register');
        $this->assertStringContainsString('/register', $url);
    }

    public function test_forgot_password_route_has_correct_name()
    {
        $url = route('password.request');
        $this->assertStringContainsString('/forgot-password', $url);
    }

    public function test_reset_password_route_has_correct_name()
    {
        $token = 'test-token';
        $url = route('password.reset', ['token' => $token]);
        $this->assertStringContainsString('/reset-password/', $url);
        $this->assertStringContainsString($token, $url);
    }

    public function test_logout_route_has_correct_name()
    {
        $url = route('logout');
        $this->assertStringContainsString('/logout', $url);
    }
}
