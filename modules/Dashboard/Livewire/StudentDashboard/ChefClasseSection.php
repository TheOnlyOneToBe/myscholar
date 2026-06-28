<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use App\Services\ModuleManager;

class ChefClasseSection extends Component
{
    public $chefClasseData = [];
    public $isChefClasse = false;
    public $moduleAvailable = false;
    public $moduleError = '';

    public function mount(): void
    {
        $this->checkModuleAvailability();
    }

    private function checkModuleAvailability(): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('chef_classe')) {
            $this->isChefClasse = false;
            return;
        }

        $moduleManager = app(ModuleManager::class);

        // Chef de classe needs access to multiple modules
        $requiredModules = ['Students', 'Grades', 'Attendance', 'Classes'];
        $missingModules = [];

        foreach ($requiredModules as $module) {
            if (!$moduleManager->canUseModule($module)) {
                $missingModules[] = $module;
            }
        }

        if (!empty($missingModules)) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Missing modules: ' . implode(', ', $missingModules);
            return;
        }

        $this->isChefClasse = true;
        $this->moduleAvailable = true;
        $this->loadChefClasseData();
    }

    private function loadChefClasseData(): void
    {
        try {
            $service = app(StudentDashboardService::class);
            $this->chefClasseData = $service->getChefClasseData();
        } catch (\Exception $e) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Error loading chef de classe data: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.chef-classe-section');
    }
}
