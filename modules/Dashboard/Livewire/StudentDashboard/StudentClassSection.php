<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use App\Services\ModuleManager;

class StudentClassSection extends Component
{
    public $classInfo = [];
    public $moduleAvailable = false;
    public $moduleError = '';

    public function mount(): void
    {
        $this->checkModuleAvailability();
    }

    private function checkModuleAvailability(): void
    {
        $moduleManager = app(ModuleManager::class);

        if (!$moduleManager->canUseModule('Classes')) {
            $this->moduleAvailable = false;
            $this->moduleError = $moduleManager->getModuleError('Classes');
            return;
        }

        $user = auth()->user();
        if (!$user || !$user->hasRole('student')) {
            $this->moduleAvailable = false;
            $this->moduleError = 'You do not have permission to view class information';
            return;
        }

        $this->moduleAvailable = true;
        $this->loadClassData();
    }

    private function loadClassData(): void
    {
        try {
            $service = app(StudentDashboardService::class);
            $this->classInfo = $service->getClassInformation();
        } catch (\Exception $e) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Error loading class information: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-class-section');
    }
}
