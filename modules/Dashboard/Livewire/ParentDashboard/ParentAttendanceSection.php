<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ParentDashboardService;

class ParentAttendanceSection extends Component
{
    public $selectedChildId = null;
    public $attendanceSummary = [];
    public $unjustifiedAbsences = [];
    public $childName = '';

    protected $listeners = ['childSelected' => 'updateSelectedChild'];

    public function mount(?int $childId = null): void
    {
        if ($childId) {
            $this->selectedChildId = $childId;
        }
        $this->loadAttendanceData();
    }

    public function updateSelectedChild(int $childId): void
    {
        $this->selectedChildId = $childId;
        $this->loadAttendanceData();
    }

    private function loadAttendanceData(): void
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

            $this->attendanceSummary = $service->getChildAttendanceSummary($this->selectedChildId);
            $this->unjustifiedAbsences = $service->getChildUnjustifiedAbsences($this->selectedChildId);

            $children = $service->getChildren();
            $child = collect($children)->firstWhere('id', $this->selectedChildId);
            $this->childName = $child['full_name'] ?? 'Étudiant';

        } catch (\Exception $e) {
            \Log::error('Error loading parent attendance: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-attendance-section');
    }
}
