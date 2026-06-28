<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ClassComparisonService;

class ClassComparisonCard extends Component
{
    public $comparisonData = [];
    public $loading = true;

    public function mount(): void
    {
        $service = app(ClassComparisonService::class);
        $this->comparisonData = $service->getClassComparison();
        $this->loading = false;
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.class-comparison-card');
    }
}
