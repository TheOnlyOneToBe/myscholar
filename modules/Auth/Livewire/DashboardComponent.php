<?php

namespace Modules\Auth\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Auth\Models\User;

#[Layout('auth::layouts.app')]
class DashboardComponent extends Component
{
    public User $user;

    public function mount()
    {
        $this->user = auth()->user();

        // Redirect based on user role
        $this->redirectByRole($this->user);
    }

    /**
     * Redirect user to appropriate dashboard based on their role(s)
     */
    private function redirectByRole(User $user): void
    {
        // Student dashboard - highest priority if student
        if ($user->hasRole('student')) {
            redirect()->to(route('student.dashboard'))->send();
            exit;
        }

        // Admin/Proviseur/Censeur dashboard
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'censeur', 'prof_principal'])) {
            redirect()->to(route('admin.dashboard'))->send();
            exit;
        }

        // Teachers and other staff
        if ($user->hasAnyRole(['teacher', 'enseignant'])) {
            redirect()->to(route('admin.dashboard'))->send();
            exit;
        }

        // Parents - show parent dashboard (to be implemented)
        if ($user->hasRole('parent')) {
            // Redirect to parent dashboard
            redirect()->to(route('admin.dashboard'))->send();
            exit;
        }

        // Default: show general dashboard
    }

    public function render()
    {
        return view('auth::livewire.dashboard');
    }
}
