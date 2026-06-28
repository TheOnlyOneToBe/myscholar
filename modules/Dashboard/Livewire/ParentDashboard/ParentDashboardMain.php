<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ParentDashboardService;

class ParentDashboardMain extends Component
{
    public $children = [];
    public $globalStats = [];
    public $alerts = [];
    public $activeChild = null;
    public $activeTab = 'overview';

    public function mount(): void
    {
        $this->loadDashboardData();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function selectChild(int $childId): void
    {
        $this->activeChild = $childId;
        $this->activeTab = 'overview';
        $this->dispatch('childSelected', $childId);
    }

    private function loadDashboardData(): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('parent')) {
            return;
        }

        try {
            $service = app(ParentDashboardService::class);

            $this->children = $service->getChildren();
            $this->globalStats = $service->getGlobalStats();
            $this->alerts = $service->getAlerts();

            if (!empty($this->children) && !$this->activeChild) {
                $this->activeChild = $this->children[0]['id'];
            }

        } catch (\Exception $e) {
            \Log::error('Error loading parent dashboard: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-dashboard-main');
    }
}
