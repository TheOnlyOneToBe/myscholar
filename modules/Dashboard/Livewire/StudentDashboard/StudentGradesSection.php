<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use Modules\Dashboard\Services\ModuleAvailabilityService;
use Modules\Config\Models\AcademicPeriod;
use App\Services\ModuleManager;

class StudentGradesSection extends Component
{
    public $recentGrades = [];
    public $subjectPerformance = [];
    public $gradeTrend = [];
    public $pendingAppeals = [];
    public $moduleAvailable = false;
    public $moduleError = '';
    public $academicPeriods = [];
    public $selectedPeriodId = null;
    public $selectedPeriodName = '';
    public $currentTerm = null;

    public function mount(): void
    {
        $this->checkModuleAvailability();
        $this->loadAcademicPeriods();
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

    private function loadAcademicPeriods(): void
    {
        try {
            // Récupérer les trimestres et semestres disponibles
            $periods = AcademicPeriod::where('academic_year', now()->year)
                ->where('is_active', true)
                ->orderBy('type')
                ->orderBy('order')
                ->get();

            $this->academicPeriods = $periods->map(function ($period) {
                return [
                    'id' => $period->id,
                    'name' => $period->name,
                    'type' => $period->type,
                    'start_date' => $period->start_date->format('d/m/Y'),
                    'end_date' => $period->end_date->format('d/m/Y'),
                    'status' => $period->getStatus(),
                ];
            })->toArray();

            // Définir le trimestre/semestre courant
            $current = $periods->firstWhere('is_active', true);
            if ($current) {
                $this->currentTerm = $current;
                $this->selectedPeriodId = $current->id;
                $this->selectedPeriodName = $current->name;
            } elseif (!empty($this->academicPeriods)) {
                $this->selectedPeriodId = $this->academicPeriods[0]['id'];
                $this->selectedPeriodName = $this->academicPeriods[0]['name'];
            }
        } catch (\Exception $e) {
            // Silencieusement échouer si les périodes ne peuvent pas être chargées
            \Log::warning('Could not load academic periods: ' . $e->getMessage());
        }
    }

    public function updatedSelectedPeriodId(): void
    {
        // Mise à jour du nom du trimestre sélectionné
        foreach ($this->academicPeriods as $period) {
            if ($period['id'] == $this->selectedPeriodId) {
                $this->selectedPeriodName = $period['name'];
                break;
            }
        }

        // Recharger les données avec la nouvelle période
        $this->loadGradesData();
    }

    private function loadGradesData(): void
    {
        try {
            $service = app(StudentDashboardService::class);

            if (auth()->user()->hasRole('student')) {
                // Passer la période académique sélectionnée au service
                $this->recentGrades = $service->getRecentGrades(5, $this->selectedPeriodId);
                $this->subjectPerformance = $service->getSubjectPerformance($this->selectedPeriodId);
                $this->gradeTrend = $service->getGradeTrend(6, $this->selectedPeriodId);
                $this->pendingAppeals = $service->getPendingAppeals();
            }
        } catch (\Exception $e) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Error loading grades: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-grades-section', [
            'academicPeriods' => $this->academicPeriods,
            'selectedPeriodId' => $this->selectedPeriodId,
            'selectedPeriodName' => $this->selectedPeriodName,
        ]);
    }
}
