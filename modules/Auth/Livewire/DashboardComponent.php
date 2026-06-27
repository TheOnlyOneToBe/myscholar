<?php

namespace Modules\Auth\Livewire;

use Livewire\Component;
use Modules\Auth\Models\User;

class DashboardComponent extends Component
{
    public User $user;

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function render()
    {
        return view('auth.livewire.dashboard')
            ->layout('auth.layouts.app');
    }
}
