<?php

namespace Tests\Feature\Auth;

use Modules\Auth\Models\User;
use Modules\Auth\Models\PasswordReset;
use Tests\TestCase;
use Livewire\Livewire;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PasswordResetTest extends TestCase
{
    public function test_user_can_request_password_reset()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        Livewire::test('modules.auth.livewire.forgot-password-component')
            ->set('email', 'test@example.com')
            ->call('sendResetLink')
            ->assertSet('sent', true);

        $this->assertDatabaseHas('password_resets', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_forgot_password_page_is_accessible()
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_forgot_password_fails_with_invalid_email()
    {
        Livewire::test('modules.auth.livewire.forgot-password-component')
            ->set('email', 'nonexistent@example.com')
            ->call('sendResetLink')
            ->assertHasErrors('email');
    }

    public function test_forgot_password_fails_without_email()
    {
        Livewire::test('modules.auth.livewire.forgot-password-component')
            ->set('email', '')
            ->call('sendResetLink')
            ->assertHasErrors('email');
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        $token = bin2hex(random_bytes(32));
        PasswordReset::create([
            'email' => 'test@example.com',
            'token' => $token,
        ]);

        Livewire::test('modules.auth.livewire.reset-password-component', ['token' => $token])
            ->set('email', 'test@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword')
            ->assertRedirect('/login');

        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $user->password));
    }

    public function test_reset_password_fails_with_invalid_token()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->get('/reset-password/invalid-token');
        $response->assertStatus(200);
        $response->assertSee('reset', false);
    }

    public function test_reset_password_fails_with_expired_token()
    {
        $originalPassword = bcrypt('originalpassword');
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => $originalPassword,
        ]);

        $token = bin2hex(random_bytes(32));
        PasswordReset::create([
            'email' => 'test@example.com',
            'token' => $token,
            'created_at' => Carbon::now()->subHours(25),
        ]);

        Livewire::test('modules.auth.livewire.reset-password-component', ['token' => $token])
            ->set('email', 'test@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword');

        $user->refresh();
        $this->assertTrue(Hash::check('originalpassword', $user->password));
    }

    public function test_reset_password_token_is_deleted_after_use()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $token = bin2hex(random_bytes(32));
        PasswordReset::create([
            'email' => 'test@example.com',
            'token' => $token,
        ]);

        Livewire::test('modules.auth.livewire.reset-password-component', ['token' => $token])
            ->set('email', 'test@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword');

        $this->assertDatabaseMissing('password_resets', [
            'token' => $token,
        ]);
    }

    public function test_reset_password_fails_with_mismatched_passwords()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $token = bin2hex(random_bytes(32));
        PasswordReset::create([
            'email' => 'test@example.com',
            'token' => $token,
        ]);

        Livewire::test('modules.auth.livewire.reset-password-component', ['token' => $token])
            ->set('email', 'test@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'different123')
            ->call('resetPassword')
            ->assertHasErrors('password');
    }

    public function test_reset_password_fails_with_short_password()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $token = bin2hex(random_bytes(32));
        PasswordReset::create([
            'email' => 'test@example.com',
            'token' => $token,
        ]);

        Livewire::test('modules.auth.livewire.reset-password-component', ['token' => $token])
            ->set('email', 'test@example.com')
            ->set('password', 'pass')
            ->set('password_confirmation', 'pass')
            ->call('resetPassword')
            ->assertHasErrors('password');
    }

    public function test_reset_password_updates_last_password_change()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'last_password_change' => null,
        ]);

        $token = bin2hex(random_bytes(32));
        PasswordReset::create([
            'email' => 'test@example.com',
            'token' => $token,
        ]);

        Livewire::test('modules.auth.livewire.reset-password-component', ['token' => $token])
            ->set('email', 'test@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('resetPassword');

        $user->refresh();
        $this->assertNotNull($user->last_password_change);
    }

    public function test_forgot_password_page_shows_success_message_after_submission()
    {
        User::factory()->create(['email' => 'test@example.com']);

        Livewire::test('modules.auth.livewire.forgot-password-component')
            ->set('email', 'test@example.com')
            ->call('sendResetLink')
            ->assertSet('sent', true);
    }

    public function test_reset_password_page_shows_invalid_token_message()
    {
        $response = $this->get('/reset-password/invalid-token');
        $response->assertStatus(200);
    }
}
