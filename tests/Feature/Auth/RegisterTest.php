<?php

namespace Tests\Feature\Auth;

use Modules\Auth\Models\User;
use Tests\TestCase;
use Livewire\Livewire;

class RegisterTest extends TestCase
{
    public function test_register_page_is_accessible()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_user_can_view_register_form()
    {
        $this->get('/register')
            ->assertSee('register', false);
    }

    public function test_user_can_register_with_valid_data()
    {
        $component = Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register');

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'username' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_register_fails_without_first_name()
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

    public function test_register_fails_without_last_name()
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

    public function test_register_fails_with_invalid_email()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'not-an-email')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('email');
    }

    public function test_register_fails_with_duplicate_email()
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

    public function test_register_fails_with_duplicate_username()
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

    public function test_register_fails_with_short_password()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'pass')
            ->set('password_confirmation', 'pass')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('password');
    }

    public function test_register_fails_with_mismatched_passwords()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password456')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('password');
    }

    public function test_register_fails_without_terms_agreement()
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

    public function test_authenticated_user_cannot_access_register_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/register');
        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_registered_user_is_active_by_default()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register');

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'is_active' => true,
        ]);
    }

    public function test_register_fails_with_short_first_name()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'J')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'johndoe')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('first_name');
    }

    public function test_register_fails_with_short_username()
    {
        Livewire::test('modules.auth.livewire.register-component')
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->set('username', 'jd')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('agree_terms', true)
            ->call('register')
            ->assertHasErrors('username');
    }
}
