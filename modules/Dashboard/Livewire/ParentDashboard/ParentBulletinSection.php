<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ParentDashboardService;

class ParentBulletinSection extends Component
{
    public $selectedChildId = null;
    public $bulletins = [];
    public $childName = '';

    protected $listeners = ['childSelected' => 'updateSelectedChild'];

    public function mount(?int $childId = null): void
    {
        if ($childId) {
            $this->selectedChildId = $childId;
        }
        $this->loadBulletins();
    }

    public function updateSelectedChild(int $childId): void
    {
        $this->selectedChildId = $childId;
        $this->loadBulletins();
    }

    private function loadBulletins(): void
    {
        if (!$this->selectedChildId) {
            return;
        }

        $user = auth()->user();

        if (!$user || !$user->hasRole('parent')) {
            return;
        }

        try {
            $service = app(ParentDashboardService::class);

            $this->bulletins = $service->getChildBulletins($this->selectedChildId);

            $children = $service->getChildren();
            $child = collect($children)->firstWhere('id', $this->selectedChildId);
            $this->childName = $child['full_name'] ?? 'Étudiant';

        } catch (\Exception $e) {
            \Log::error('Error loading parent bulletins: ' . $e->getMessage());
        }
    }

    public function downloadBulletin(int $bulletinId): void
    {
        try {
            $service = app(\Modules\Dashboard\Services\DocumentGenerationService::class);
            \Log::info("Download bulletin {$bulletinId} for child {$this->selectedChildId}");

        } catch (\Exception $e) {
            \Log::error('Error downloading bulletin: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-bulletin-section');
    }
}
