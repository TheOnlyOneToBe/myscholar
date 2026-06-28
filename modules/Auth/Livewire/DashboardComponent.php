<?php

namespace Modules\Auth\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Auth\Models\User;
use Illuminate\Routing\Redirector;

#[Layout('auth::layouts.app')]
class DashboardComponent extends Component
{
    public User $user;

    public function mount()
    {
        $this->user = auth()->user();

        // Redirect based on user role
        return $this->redirectByRole($this->user);
    }

    /**
     * Redirect user to appropriate dashboard based on their role(s)
     */
    private function redirectByRole(User $user): Redirector
    {
        // Student dashboard - highest priority if student
        if ($user->hasRole('student')) {
            return redirect(route('student.dashboard'));
        }

        // Parents - show parent dashboard
        if ($user->hasRole('parent')) {
            return redirect(route('parent.dashboard'));
        }

        // Admin/Proviseur/Censeur dashboard
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'censeur'])) {
            return redirect(route('admin.dashboard'));
        }

        // Teachers and academic staff
        if ($user->hasAnyRole(['enseignant', 'prof_principal', 'surveillant'])) {
            return redirect(route('admin.dashboard'));
        }

        // Other staff members
        if ($user->hasAnyRole(['secretaire', 'comptable', 'infirmier', 'bibliothecaire', 'gardien'])) {
            return redirect(route('admin.dashboard'));
        }

        // Default: redirect to admin dashboard
        return redirect(route('admin.dashboard'));
    }

    public function render()
    {
        return view('auth::livewire.dashboard');
    }
}
