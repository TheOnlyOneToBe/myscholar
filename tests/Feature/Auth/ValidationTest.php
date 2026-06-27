<?php

namespace Tests\Feature\Auth;

use Modules\Auth\Models\User;
use Tests\TestCase;
use Livewire\Livewire;

class ValidationTest extends TestCase
{
    /**
     * Login Validation Tests
     */
    public function test_login_email_is_required()
    {
        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', '')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');
    }

    public function test_login_email_must_be_valid()
    {
        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'invalid-email')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors('email');
    }

    public function test_login_password_is_required()
    {
        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors('password');
    }

    public function test_login_password_minimum_length()
    {
        Livewire::test('modules.auth.livewire.login-component')
            ->set('email', 'test@example.com')
            ->set('password', 'short')
            ->call('login')
            ->assertHasErrors('password');
    }

    /**
     * Register Validation Tests
     */
    public function test_register_first_name_is_required()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', '')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('first_name');
    }

    public function test_register_last_name_is_required()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', '')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('last_name');
    }

    public function test_register_email_is_required()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', '')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('email');
    }

    public function test_register_email_must_be_valid()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'invalid-email')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('email');
    }

    public function test_register_email_must_be_unique()
    {
        User::factory()->create(['email' => 'john@example.com']);

        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('email');
    }

    public function test_register_username_is_required()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', '')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('username');
    }

    public function test_register_username_must_be_unique()
    {
        User::factory()->create(['username' => 'johndoe']);

        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('username');
    }

    public function test_register_password_is_required()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('password');
    }

    public function test_register_password_confirmation_is_required()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', '')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('password');
    }

    public function test_register_password_must_match_confirmation()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('password');
    }

    public function test_register_terms_must_be_accepted()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', false)
            ->call('register')
            ->assertHasErrors('agree_terms');
    }

    /**
     * Forgot Password Validation Tests
     */
    public function test_forgot_password_email_is_required()
    {
        Livewire::test('modules.auth.livewire.forgot-password-component')
            ->set('email', '')
            ->call('sendResetLink')
            ->assertHasErrors('email');
    }

    public function test_forgot_password_email_must_be_valid()
    {
        Livewire::test('modules.auth.livewire.forgot-password-component')
            ->set('email', 'invalid-email')
            ->call('sendResetLink')
            ->assertHasErrors('email');
    }

    public function test_forgot_password_email_must_exist()
    {
        Livewire::test('modules.auth.livewire.forgot-password-component')
            ->set('email', 'nonexistent@example.com')
            ->call('sendResetLink')
            ->assertHasErrors('email');
    }

    /**
     * Reset Password Validation Tests
     */
    public function test_reset_password_email_is_required()
    {
        $token = bin2hex(random_bytes(32));

        Livewire::test('modules.auth.livewire.reset-password-component', ['token' => $token])
            ->set('email', '')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertHasErrors('email');
    }

    public function test_reset_password_password_is_required()
    {
        $token = bin2hex(random_bytes(32));

        Livewire::test('modules.auth.livewire.reset-password-component', ['token' => $token])
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('resetPassword')
            ->assertHasErrors('password');
    }

    public function test_reset_password_confirmation_is_required()
    {
        $token = bin2hex(random_bytes(32));

        Livewire::test('modules.auth.livewire.reset-password-component', ['token' => $token])
            ->set('email', 'test@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', '')
            ->call('resetPassword')
            ->assertHasErrors('password');
    }
}
