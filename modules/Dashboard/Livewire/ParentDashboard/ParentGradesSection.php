<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ParentDashboardService;

class ParentGradesSection extends Component
{
    public $selectedChildId = null;
    public $recentGrades = [];
    public $subjectPerformance = [];
    public $childAverage = 0;
    public $childName = '';

    protected $listeners = ['childSelected' => 'updateSelectedChild'];

    public function mount(?int $childId = null): void
    {
        if ($childId) {
            $this->selectedChildId = $childId;
        }
        $this->loadGradesData();
    }

    public function updateSelectedChild(int $childId): void
    {
        $this->selectedChildId = $childId;
        $this->loadGradesData();
    }

    private function loadGradesData(): void
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

            $this->recentGrades = $service->getChildRecentGrades($this->selectedChildId);
            $this->subjectPerformance = $service->getChildSubjectPerformance($this->selectedChildId);
            $this->childAverage = $service->getChildAverage($this->selectedChildId);

            $children = $service->getChildren();
            $child = collect($children)->firstWhere('id', $this->selectedChildId);
            $this->childName = $child['full_name'] ?? 'Étudiant';

        } catch (\Exception $e) {
            \Log::error('Error loading parent grades: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-grades-section');
    }
}
