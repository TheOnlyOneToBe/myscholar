<?php

namespace Modules\Auth\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Modules\Auth\Models\User;
use Modules\Auth\Models\PasswordReset;

class ForgotPasswordComponent extends Component
{
    #[Rule('required|email|exists:users,email')]
    public string $email = '';

    public bool $sent = false;

    public function sendResetLink()
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if ($user) {
            $token = bin2hex(random_bytes(32));

            PasswordReset::updateOrCreate(
                ['email' => $this->email],
                [
                    'token' => $token,
                    'created_at' => now(),
                ]
            );

            // In a real application, send email with reset link
            // Mail::send('emails.reset-password', ['token' => $token], function($message) use ($user) {
            //     $message->to($user->email)->subject('Reset Your Password');
            // });

            $this->sent = true;
            $this->email = '';
        }
    }

    public function render()
    {
        return view('auth.livewire.forgot-password')
            ->layout('auth.layouts.app');
    }
}
