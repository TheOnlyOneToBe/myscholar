<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\MultiTermComparisonService;

class MultiTermComparisonCard extends Component
{
    public $comparisonData = [];
    public $activeTab = 'overview';
    public $loading = true;

    public function mount()
    {
        $this->loadComparisonData();
        $this->loading = false;
    }

    public function switchTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    private function loadComparisonData()
    {
        $comparisonService = app(MultiTermComparisonService::class);
        $this->comparisonData = $comparisonService->getTermComparison();
    }

    public function downloadBulletin(string $term)
    {
        return redirect()->route('student.bulletin.download', ['term' => $term]);
    }

    public function render()
    {
        return view('livewire.student-dashboard.multi-term-comparison-card');
    }
}
