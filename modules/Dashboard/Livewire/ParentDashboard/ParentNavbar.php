<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;

class ParentNavbar extends Component
{
    public $parentName = '';
    public $parentEmail = '';
    public $childrenCount = 0;
    public $notificationCount = 0;
    public $showProfileDropdown = false;
    public $showNotifications = false;

    public function mount(): void
    {
        $this->loadParentInfo();
    }

    private function loadParentInfo(): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('parent')) {
            return;
        }

        try {
            $this->parentName = $user->name ?? 'Parent';
            $this->parentEmail = $user->email ?? '';

            $service = app(\Modules\Dashboard\Services\ParentDashboardService::class);
            $children = $service->getChildren();
            $this->childrenCount = count($children);
            $this->notificationCount = 0;

        } catch (\Exception $e) {
            \Log::error('Error loading parent navbar: ' . $e->getMessage());
        }
    }

    public function toggleProfileDropdown(): void
    {
        $this->showProfileDropdown = !$this->showProfileDropdown;
        $this->showNotifications = false;
    }

    public function toggleNotifications(): void
    {
        $this->showNotifications = !$this->showNotifications;
        $this->showProfileDropdown = false;
    }

    public function logout(): void
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        redirect()->route('login');
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-navbar');
    }
}
