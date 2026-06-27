<?php

namespace Modules\Auth\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Layout;
use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Auth;

#[Layout('auth::layouts.app')]
class LoginComponent extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:8')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return $this->redirect('/dashboard', navigate: true);
        }

        $this->addError('email', 'These credentials do not match our records.');
    }

    public function render()
    {
        return view('auth::livewire.login');
    }
}
