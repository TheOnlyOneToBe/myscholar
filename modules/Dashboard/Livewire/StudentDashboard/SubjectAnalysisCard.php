<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\SubjectAnalysisService;

class SubjectAnalysisCard extends Component
{
    public $analysis = [];
    public $loading = true;

    public function mount(): void
    {
        $service = app(SubjectAnalysisService::class);
        $this->analysis = $service->getSubjectAnalysis();
        $this->loading = false;
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.subject-analysis-card');
    }
}
