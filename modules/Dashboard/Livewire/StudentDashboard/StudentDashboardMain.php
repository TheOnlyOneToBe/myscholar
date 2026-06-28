<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use Modules\Dashboard\Services\ModuleAvailabilityService;
use App\Services\ModuleManager;

class StudentDashboardMain extends Component
{
    public $studentInfo = [];
    public $quickStats = [];
    public $availableModules = [];
    public $isChefClasse = false;
    public $activeTab = 'overview';

    public function mount(): void
    {
        $this->loadDashboardData();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    private function loadDashboardData(): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('student')) {
            return;
        }

        try {
            $service = app(StudentDashboardService::class);
            $availabilityService = app(ModuleAvailabilityService::class);

            $this->studentInfo = $service->getStudentInfo();
            $this->quickStats = $service->getQuickStats();
            $this->isChefClasse = $service->isChefClasse();

            // Get available modules for student role
            $this->availableModules = $availabilityService->getAvailableModulesForRole('student');

        } catch (\Exception $e) {
            \Log::error('Error loading student dashboard: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-dashboard-main');
    }
}
