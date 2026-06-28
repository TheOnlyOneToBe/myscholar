<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ParentDashboardService;
use Modules\Config\Models\AcademicPeriod;

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
        if (!$this->selectedChildId) {
            $this->dispatch('error', 'No child selected');
            return;
        }

        try {
            $period = AcademicPeriod::find($bulletinId);

            if (!$period) {
                $this->dispatch('error', 'Bulletin not found');
                return;
            }

            $redirectUrl = route('dashboard.term-documents.bulletin.download', [
                'studentId' => $this->selectedChildId,
                'academicPeriodId' => $bulletinId,
            ]);

            $this->redirect($redirectUrl);
        } catch (\Exception $e) {
            \Log::error('Error downloading bulletin: ' . $e->getMessage());
            $this->dispatch('error', 'Error downloading bulletin');
        }
    }

    public function previewBulletin(int $bulletinId): void
    {
        if (!$this->selectedChildId) {
            $this->dispatch('error', 'No child selected');
            return;
        }

        try {
            $redirectUrl = route('dashboard.term-documents.bulletin.preview', [
                'studentId' => $this->selectedChildId,
                'academicPeriodId' => $bulletinId,
            ]);

            $this->redirect($redirectUrl, navigate: true);
        } catch (\Exception $e) {
            \Log::error('Error previewing bulletin: ' . $e->getMessage());
            $this->dispatch('error', 'Error previewing bulletin');
        }
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-bulletin-section');
    }
}
