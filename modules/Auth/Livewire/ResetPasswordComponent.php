<?php

namespace Modules\Auth\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Layout;
use Modules\Auth\Models\PasswordReset;
use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

#[Layout('auth::layouts.app')]
class ResetPasswordComponent extends Component
{
    public string $token = '';

    #[Rule('required|email|exists:users,email')]
    public string $email = '';

    #[Rule('required|min:8|confirmed')]
    public string $password = '';

    #[Rule('required')]
    public string $password_confirmation = '';

    public bool $invalid_token = false;

    public function mount($token)
    {
        $this->token = $token;

        $reset = PasswordReset::where('token', $token)->first();

        if (!$reset || now()->diffInHours($reset->created_at) > 24) {
            $this->invalid_token = true;
        }
    }

    public function resetPassword()
    {
        $this->validate();

        $reset = PasswordReset::where('token', $this->token)
            ->where('email', $this->email)
            ->first();

        if (!$reset || now()->diffInHours($reset->created_at) > 24) {
            $this->addError('email', 'This password reset link is invalid or has expired.');
            return;
        }

        $user = User::where('email', $this->email)->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($this->password),
                'last_password_change' => now(),
            ]);

            $reset->delete();

            session()->flash('success', 'Your password has been reset. Please log in.');
            return $this->redirect(route('login'), navigate: true);
        }
    }

    public function render()
    {
        return view('auth::livewire.reset-password');
    }
}
