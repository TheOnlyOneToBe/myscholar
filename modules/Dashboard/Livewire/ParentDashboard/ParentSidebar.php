<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ParentDashboardService;

class ParentSidebar extends Component
{
    public $children = [];
    public $activeChild = null;
    public $activeTab = 'overview';
    public $sidebarOpen = true;

    protected $listeners = ['childSelected' => 'updateActiveChild'];

    public function mount(): void
    {
        $this->loadSidebarData();
    }

    private function loadSidebarData(): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('parent')) {
            return;
        }

        try {
            $service = app(ParentDashboardService::class);
            $this->children = $service->getChildren();

            if (!empty($this->children) && !$this->activeChild) {
                $this->activeChild = $this->children[0]['id'];
            }

        } catch (\Exception $e) {
            \Log::error('Error loading parent sidebar: ' . $e->getMessage());
        }
    }

    public function updateActiveChild(int $childId): void
    {
        $this->activeChild = $childId;
    }

    public function selectTab(string $tab, ?int $childId = null): void
    {
        $this->activeTab = $tab;
        if ($childId) {
            $this->activeChild = $childId;
        }
        $this->dispatch('tabChanged', $tab, $childId);
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = !$this->sidebarOpen;
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-sidebar');
    }
}
