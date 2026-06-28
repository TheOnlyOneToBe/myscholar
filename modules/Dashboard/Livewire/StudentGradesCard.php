<?php

namespace Modules\Dashboard\Livewire;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;

class StudentGradesCard extends Component
{
    public $recentGrades = [];
    public $subjectPerformance = [];
    public $gradeTrend = [];
    public $pendingAppeals = [];
    public $showDetails = false;

    public function mount(): void
    {
        $this->loadGradesData();
    }

    public function toggleDetails(): void
    {
        $this->showDetails = !$this->showDetails;
    }

    private function loadGradesData(): void
    {
        $service = app(StudentDashboardService::class);

        $this->recentGrades = $service->getRecentGrades(5);
        $this->subjectPerformance = $service->getSubjectPerformance();
        $this->gradeTrend = $service->getGradeTrend(6);
        $this->pendingAppeals = $service->getPendingAppeals();
    }

    public function render()
    {
        return view('dashboard::livewire.student-grades-card');
    }
}
