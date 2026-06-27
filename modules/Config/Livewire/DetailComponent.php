<?php

namespace Modules\Config\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Config\Models\SchoolInfo;
use Modules\Config\Models\SystemSetting;
use Modules\Config\Models\SchoolYear;
use Illuminate\Support\Facades\Storage;

#[Layout('config::layouts.app')]
class DetailComponent extends Component
{
    public ?SchoolInfo $schoolInfo = null;
    public array $systemSettings = [];
    public ?SchoolYear $currentSchoolYear = null;
    public array $allSchoolYears = [];
    public bool $editMode = false;
    public array $formData = [];

    public function mount(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        $this->schoolInfo = SchoolInfo::current();
        $this->systemSettings = SystemSetting::getByGroup('general');
        $this->currentSchoolYear = SchoolYear::where('is_active', true)->first();
        $this->allSchoolYears = SchoolYear::orderBy('year', 'desc')->get()->toArray();

        if ($this->schoolInfo) {
            $this->formData = $this->schoolInfo->toArray();
        }
    }

    public function toggleEditMode(): void
    {
        if ($this->editMode) {
            $this->loadData();
        }
        $this->editMode = !$this->editMode;
    }

    public function updateSchoolInfo(): void
    {
        $this->authorize('config.school_info.edit');

        $validated = $this->validate([
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.acronym' => ['nullable', 'string', 'max:50'],
            'formData.motto' => ['nullable', 'string', 'max:255'],
            'formData.school_type' => ['required', 'in:public,prive,confessionnel'],
            'formData.address' => ['nullable', 'string'],
            'formData.city' => ['nullable', 'string'],
            'formData.region' => ['nullable', 'string'],
            'formData.phone' => ['nullable', 'string'],
            'formData.email' => ['nullable', 'email'],
            'formData.website' => ['nullable', 'url'],
            'formData.po_box' => ['nullable', 'string'],
            'formData.approval_number' => ['nullable', 'string'],
            'formData.creation_decree' => ['nullable', 'string'],
            'formData.founder_name' => ['nullable', 'string'],
            'formData.director_name' => ['nullable', 'string'],
            'formData.foundation_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ]);

        if ($this->schoolInfo) {
            $this->schoolInfo->update($validated['formData']);
        } else {
            SchoolInfo::create($validated['formData']);
            $this->schoolInfo = SchoolInfo::current();
        }

        $this->dispatch('notify', message: 'Informations du lycée mises à jour avec succès.');
        $this->editMode = false;
    }

    public function getSystemSetting(string $key, mixed $default = null): mixed
    {
        return SystemSetting::get($key, $default);
    }

    public function render()
    {
        return view('config::livewire.detail');
    }
}
