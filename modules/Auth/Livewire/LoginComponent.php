<?php

namespace Modules\Auth\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Layout;
use Modules\Auth\Models\User;
use Modules\Auth\Services\RedirectService;
use Illuminate\Support\Facades\Auth;

#[Layout('auth::layouts.app')]
class LoginComponent extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:8')]
    public string $password = '';

    public bool $remember = false;

    public function __construct(
        private RedirectService $redirectService,
    ) {
        parent::__construct();
    }

    public function login()
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            $this->addError('email', 'These credentials do not match our records.');
            return;
        }

        if (!$user->is_active) {
            $this->addError('email', 'This account is inactive.');
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $user = auth()->user();
            $redirectPath = $this->redirectService->getRedirectPath($user);

            return $this->redirect($redirectPath, navigate: true);
        }

        $this->addError('email', 'These credentials do not match our records.');
    }

    public function render()
    {
        return view('auth::livewire.login');
    }
}
