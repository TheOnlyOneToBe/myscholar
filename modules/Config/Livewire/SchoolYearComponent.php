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
        try {
            $sessionService->initializeSession();
        } catch (\Exception $e) {
            \Log::warning('Failed to initialize school year session', ['error' => $e->getMessage()]);
        }
        $this->sessionYear = $sessionService->getActiveYear();
    }

    public function loadSchoolYears(): void
    {
        $this->schoolYears = SchoolYear::orderBy('start_year', 'desc')->get();
    }

    public function toggleForm(): void
    {
        if (!auth()->user()->can('config.school_year.create') && !$this->editingYear) {
            $this->error(__('config.errors.permission_denied_past_year'), 'PERMISSION_DENIED');
            return;
        }

        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function startEdit(SchoolYear $year): void
    {
        if (!auth()->user()->can('config.school_year.edit')) {
            $this->error(__('config.errors.permission_denied_past_year'), 'PERMISSION_DENIED');
            return;
        }

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
        if (!auth()->user()->can('config.school_year.create')) {
            $this->error(__('config.errors.permission_denied_past_year'), 'PERMISSION_DENIED');
            return;
        }

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

            $this->success(__('config.messages.school_year_created'), 'SCHOOL_YEAR_CREATED');
            $this->loadSchoolYears();
            $this->resetForm();
            $this->showForm = false;
        } catch (\Exception $e) {
            \Log::error('Error creating school year', ['error' => $e->getMessage()]);
            $this->error(__('config.alerts.error_creating'), 'CREATE_ERROR');
        }
    }

    public function updateYear(): void
    {
        if (!auth()->user()->can('config.school_year.edit')) {
            $this->error(__('config.errors.permission_denied_past_year'), 'PERMISSION_DENIED');
            return;
        }

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

            $this->success(__('config.messages.school_year_updated'), 'SCHOOL_YEAR_UPDATED');
            $this->loadSchoolYears();
            $this->resetForm();
            $this->showForm = false;
        } catch (\Exception $e) {
            \Log::error('Error updating school year', ['error' => $e->getMessage()]);
            $this->error(__('config.alerts.error_updating'), 'UPDATE_ERROR');
        }
    }

    public function deleteYear(SchoolYear $year): void
    {
        if (!auth()->user()->can('config.school_year.delete')) {
            $this->error(__('config.errors.permission_denied_past_year'), 'PERMISSION_DENIED');
            return;
        }

        if ($year->is_active) {
            $this->error(__('config.alerts.cannot_delete_active'), 'CANNOT_DELETE_ACTIVE');
            return;
        }

        try {
            $year->delete();
            $this->success(__('config.messages.school_year_deleted'), 'SCHOOL_YEAR_DELETED');
            $this->loadSchoolYears();
        } catch (\Exception $e) {
            \Log::error('Error deleting school year', ['error' => $e->getMessage()]);
            $this->error(__('config.alerts.error_deleting'), 'DELETE_ERROR');
        }
    }

    public function activateYear(SchoolYear $year): void
    {
        if (!auth()->user()->can('config.school_year.edit')) {
            $this->error(__('config.errors.permission_denied_past_year'), 'PERMISSION_DENIED');
            return;
        }

        try {
            // Désactiver l'année active précédente
            SchoolYear::where('is_active', true)->update(['is_active' => false]);

            // Activer la nouvelle année
            $year->update(['is_active' => true]);

            // Mettre à jour la session aussi
            $sessionService = new SchoolYearSessionService();
            $sessionService->setActiveYear($year);

            $this->success(__('config.alerts.activated', ['name' => $year->name]), 'SCHOOL_YEAR_ACTIVATED');
            $this->loadSchoolYears();
            $this->activeYear = $year;
            $this->sessionYear = $year;

            $this->dispatch('school-year-changed', schoolYearId: $year->id);
        } catch (\Exception $e) {
            \Log::error('Error activating school year', ['error' => $e->getMessage()]);
            $this->error(__('config.alerts.error_activating'), 'ACTIVATION_ERROR');
        }
    }

    public function switchSession(SchoolYear $year): void
    {
        try {
            $sessionService = new SchoolYearSessionService();
            $sessionService->setActiveYear($year);
            $this->sessionYear = $year;
            $this->success(__('config.alerts.session_switched'), 'SESSION_SWITCHED');
            $this->dispatch('school-year-changed', schoolYearId: $year->id);
        } catch (\Exception $e) {
            \Log::error('Error switching session', ['error' => $e->getMessage()]);
            $this->error(__('config.alerts.error_switching'), 'SWITCH_ERROR');
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
        return view('config::livewire.school-year-component', [
            'canCreate' => auth()->user()->can('config.school_year.create'),
            'canEdit' => auth()->user()->can('config.school_year.edit'),
            'canDelete' => auth()->user()->can('config.school_year.delete'),
            'canSwitch' => auth()->user()->can('config.school_year.edit'),
        ]);
    }
}
