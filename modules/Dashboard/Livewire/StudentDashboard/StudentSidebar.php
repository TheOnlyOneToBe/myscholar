<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use Modules\Dashboard\Services\ModuleAvailabilityService;

class StudentSidebar extends Component
{
    public $isChefClasse = false;
    public $availableModules = [];
    public $activeTab = 'overview';
    public $sidebarOpen = true;

    protected $listeners = ['switchTab' => 'updateActiveTab'];

    public function mount(): void
    {
        $this->loadSidebarData();
    }

    private function loadSidebarData(): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('student')) {
            return;
        }

        try {
            $service = app(StudentDashboardService::class);
            $availabilityService = app(ModuleAvailabilityService::class);

            $this->isChefClasse = $service->isChefClasse();
            $this->availableModules = $availabilityService->getAvailableModulesForRole('student');

        } catch (\Exception $e) {
            \Log::error('Error loading student sidebar: ' . $e->getMessage());
        }
    }

    public function updateActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = !$this->sidebarOpen;
    }

    public function selectTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->dispatch('tabChanged', $tab);
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-sidebar');
    }
}
