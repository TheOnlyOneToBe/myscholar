<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use Modules\Dashboard\Services\ModuleAvailabilityService;
use App\Services\ModuleManager;

class StudentGradesSection extends Component
{
    public $recentGrades = [];
    public $subjectPerformance = [];
    public $gradeTrend = [];
    public $pendingAppeals = [];
    public $moduleAvailable = false;
    public $moduleError = '';

    public function mount(): void
    {
        $this->checkModuleAvailability();
    }

    private function checkModuleAvailability(): void
    {
        $moduleManager = app(ModuleManager::class);

        if (!$moduleManager->canUseModule('Grades')) {
            $this->moduleAvailable = false;
            $this->moduleError = $moduleManager->getModuleError('Grades');
            return;
        }

        $user = auth()->user();
        if (!$user || (!$user->hasRole('student') && !$user->hasRole('enseignant') && !$user->hasRole('chef_classe'))) {
            $this->moduleAvailable = false;
            $this->moduleError = 'You do not have permission to view grades';
            return;
        }

        $this->moduleAvailable = true;
        $this->loadGradesData();
    }

    private function loadGradesData(): void
    {
        try {
            $service = app(StudentDashboardService::class);

            if (auth()->user()->hasRole('student')) {
                $this->recentGrades = $service->getRecentGrades(5);
                $this->subjectPerformance = $service->getSubjectPerformance();
                $this->gradeTrend = $service->getGradeTrend(6);
                $this->pendingAppeals = $service->getPendingAppeals();
            }
        } catch (\Exception $e) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Error loading grades: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-grades-section');
    }
}
