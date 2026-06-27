<?php

namespace Modules\Config\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Modules\Config\Models\SchoolYear;
use Modules\Config\Services\SchoolYearSessionService;
use Illuminate\Support\Collection;

class SchoolYearComponent extends Component
{
    use \App\Traits\HasAlerts;

    #[Validate('required|string|max:255|unique:school_years,name')]
    public string $name = '';

    #[Validate('required|integer|min:1900|max:2100')]
    public int|string $start_year = '';

    #[Validate('required|integer|min:1900|max:2100')]
    public int|string $end_year = '';

    #[Validate('required|date')]
    public string $start_date = '';

    #[Validate('required|date|after:start_date')]
    public string $end_date = '';

    #[Validate('nullable|string')]
    public string $description = '';

    public bool $showForm = false;
    public ?SchoolYear $editingYear = null;
    public ?SchoolYear $activeYear = null;
    public ?SchoolYear $sessionYear = null;
    public Collection $schoolYears;

    public function mount(): void
    {
        $this->initializeAlerts();
        $this->loadSchoolYears();
        $this->activeYear = SchoolYear::active();

        // Initialize session if no year is selected
        $sessionService = new SchoolYearSessionService();
        $sessionService->initializeSession();
        $this->sessionYear = $sessionService->getActiveYear();
    }

    public function loadSchoolYears(): void
    {
        $this->schoolYears = SchoolYear::orderBy('start_year', 'desc')->get();
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function startEdit(SchoolYear $year): void
    {
        $this->editingYear = $year;
        $this->name = $year->name;
        $this->start_year = $year->start_year;
        $this->end_year = $year->end_year;
        $this->start_date = $year->start_date->format('Y-m-d');
        $this->end_date = $year->end_date->format('Y-m-d');
        $this->description = $year->description ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        if ($this->editingYear) {
            $this->updateYear();
        } else {
            $this->createYear();
        }
    }

    public function createYear(): void
    {
        $this->validateOnly('name');
        $this->validateOnly('start_year');
        $this->validateOnly('end_year');
        $this->validateOnly('start_date');
        $this->validateOnly('end_date');

        try {
            SchoolYear::create([
                'name' => $this->name,
                'start_year' => $this->start_year,
                'end_year' => $this->end_year,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'description' => $this->description,
                'is_active' => false,
            ]);

            $this->success('Année scolaire créée avec succès', 'SCHOOL_YEAR_CREATED');
            $this->loadSchoolYears();
            $this->resetForm();
            $this->showForm = false;
        } catch (\Exception $e) {
            $this->error('Erreur lors de la création: ' . $e->getMessage(), 'CREATE_ERROR');
        }
    }

    public function updateYear(): void
    {
        if (!$this->editingYear) {
            return;
        }

        try {
            $this->editingYear->update([
                'name' => $this->name,
                'start_year' => $this->start_year,
                'end_year' => $this->end_year,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'description' => $this->description,
            ]);

            $this->success('Année scolaire modifiée avec succès', 'SCHOOL_YEAR_UPDATED');
            $this->loadSchoolYears();
            $this->resetForm();
            $this->showForm = false;
        } catch (\Exception $e) {
            $this->error('Erreur lors de la modification: ' . $e->getMessage(), 'UPDATE_ERROR');
        }
    }

    public function deleteYear(SchoolYear $year): void
    {
        if ($year->is_active) {
            $this->error('Impossible de supprimer l\'année scolaire active', 'CANNOT_DELETE_ACTIVE');
            return;
        }

        try {
            $year->delete();
            $this->success('Année scolaire supprimée avec succès', 'SCHOOL_YEAR_DELETED');
            $this->loadSchoolYears();
        } catch (\Exception $e) {
            $this->error('Erreur lors de la suppression: ' . $e->getMessage(), 'DELETE_ERROR');
        }
    }

    public function activateYear(SchoolYear $year): void
    {
        try {
            // Désactiver l'année active précédente
            SchoolYear::where('is_active', true)->update(['is_active' => false]);

            // Activer la nouvelle année
            $year->update(['is_active' => true]);

            // Mettre à jour la session aussi
            $sessionService = new SchoolYearSessionService();
            $sessionService->setActiveYear($year);

            $this->success('Année scolaire ' . $year->name . ' activée', 'SCHOOL_YEAR_ACTIVATED');
            $this->loadSchoolYears();
            $this->activeYear = $year;
            $this->sessionYear = $year;

            $this->dispatch('school-year-changed', schoolYearId: $year->id);
        } catch (\Exception $e) {
            $this->error('Erreur lors de l\'activation: ' . $e->getMessage(), 'ACTIVATION_ERROR');
        }
    }

    public function switchSession(SchoolYear $year): void
    {
        try {
            $sessionService = new SchoolYearSessionService();
            $sessionService->setActiveYear($year);
            $this->sessionYear = $year;
            $this->success('Année en session changée vers ' . $year->name, 'SESSION_SWITCHED');
            $this->dispatch('school-year-changed', schoolYearId: $year->id);
        } catch (\Exception $e) {
            $this->error('Erreur lors du changement de session: ' . $e->getMessage(), 'SWITCH_ERROR');
        }
    }

    protected function resetForm(): void
    {
        $this->resetExcept('schoolYears', 'activeYear', 'sessionYear', 'showForm');
        $this->editingYear = null;
        $this->name = '';
        $this->start_year = '';
        $this->end_year = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->description = '';
    }

    protected function getSessionYear(): ?SchoolYear
    {
        $sessionService = new SchoolYearSessionService();
        return $sessionService->getActiveYear();
    }

    public function render()
    {
        return view('config::livewire.school-year-component');
    }
}
