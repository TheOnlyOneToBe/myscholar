<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use App\Services\ModuleManager;

class StudentAttendanceSection extends Component
{
    public $attendanceSummary = [];
    public $moduleAvailable = false;
    public $moduleError = '';

    public function mount(): void
    {
        $this->checkModuleAvailability();
    }

    private function checkModuleAvailability(): void
    {
        $moduleManager = app(ModuleManager::class);

        if (!$moduleManager->canUseModule('Attendance')) {
            $this->moduleAvailable = false;
            $this->moduleError = $moduleManager->getModuleError('Attendance');
            return;
        }

        $user = auth()->user();
        if (!$user || !$user->hasRole('student')) {
            $this->moduleAvailable = false;
            $this->moduleError = 'You do not have permission to view attendance';
            return;
        }

        $this->moduleAvailable = true;
        $this->loadAttendanceData();
    }

    private function loadAttendanceData(): void
    {
        try {
            $service = app(StudentDashboardService::class);
            $this->attendanceSummary = $service->getAttendanceSummary();
        } catch (\Exception $e) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Error loading attendance: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-attendance-section');
    }
}
