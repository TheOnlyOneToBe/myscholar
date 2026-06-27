<?php

namespace Modules\Auth\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterComponent extends Component
{
    #[Rule('required|string|min:2')]
    public string $first_name = '';

    #[Rule('required|string|min:2')]
    public string $last_name = '';

    #[Rule('required|email|unique:users,email')]
    public string $email = '';

    #[Rule('required|string|min:3|unique:users,username')]
    public string $username = '';

    #[Rule('required|min:8|confirmed')]
    public string $password = '';

    #[Rule('required')]
    public string $password_confirmation = '';

    public bool $agree_terms = false;

    public function register()
    {
        $this->validate();

        if (!$this->agree_terms) {
            $this->addError('agree_terms', 'You must agree to the terms and conditions.');
            return;
        }

        User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'is_active' => true,
        ]);

        session()->flash('success', 'Registration successful! Please log in.');
        return redirect()->route('login');
    }

    public function render()
    {
        return view('auth.livewire.register')
            ->layout('auth.layouts.app');
    }
}
