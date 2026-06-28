<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ParentDashboardService;

class ParentChildrenSection extends Component
{
    public $children = [];
    public $selectedChildId = null;

    protected $listeners = ['childSelected' => 'updateSelectedChild'];

    public function mount(): void
    {
        $this->loadChildren();
    }

    private function loadChildren(): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('parent')) {
            return;
        }

        try {
            $service = app(ParentDashboardService::class);
            $this->children = $service->getChildren();

            if (!empty($this->children) && !$this->selectedChildId) {
                $this->selectedChildId = $this->children[0]['id'];
            }

        } catch (\Exception $e) {
            \Log::error('Error loading parent children: ' . $e->getMessage());
        }
    }

    public function updateSelectedChild(int $childId): void
    {
        $this->selectedChildId = $childId;
    }

    public function selectChild(int $childId): void
    {
        $this->selectedChildId = $childId;
        $this->dispatch('childSelected', $childId);
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-children-section');
    }
}
