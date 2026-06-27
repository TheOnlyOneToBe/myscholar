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
    }

    public function render()
    {
        return view('auth::livewire.dashboard');
    }
}
