<?php

namespace Modules\Classes\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Classes\Models\ClassModel;
use Modules\Config\Models\SchoolYear;

class ClassListComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $level = '';
    public $filiere = '';
    public $school_year_id = '';
    public $sort_by = 'name';
    public $sort_order = 'asc';
    public $per_page = 25;

    protected $paginationTheme = 'tailwind';

    public $showForm = false;
    public $editingId = null;
    public $form = [
        'name' => '',
        'code' => '',
        'level' => '',
        'section' => '',
        'filiere' => '',
        'room_id' => '',
        'capacity' => 45,
        'school_year_id' => '',
        'description' => '',
    ];

    public $deleteConfirm = null;

    protected $listeners = ['classUpdated' => 'refreshClasses'];

    public function mount()
    {
        $this->school_year_id = SchoolYear::active()?->id ?? '';
        if (!$this->form['school_year_id']) {
            $this->form['school_year_id'] = $this->school_year_id;
        }
    }

    public function render()
    {
        $query = ClassModel::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%");
            });
        }

        if ($this->level) {
            $query->where('level', $this->level);
        }

        if ($this->filiere) {
            $query->where('filiere', $this->filiere);
        }

        if ($this->school_year_id) {
            $query->where('school_year_id', $this->school_year_id);
        }

        $query->orderBy($this->sort_by, $this->sort_order);
        $classes = $query->paginate($this->per_page);

        return view('classes::livewire.class-list', [
            'classes' => $classes,
            'schoolYears' => SchoolYear::all(),
            'levels' => ['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5'],
            'filieres' => ['Science', 'Littéraire', 'Commercial', 'Technique'],
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedLevel()
    {
        $this->resetPage();
    }

    public function updatedFiliere()
    {
        $this->resetPage();
    }

    public function updatedSchoolYearId()
    {
        $this->resetPage();
    }

    public function toggleSort($column)
    {
        if ($this->sort_by === $column) {
            $this->sort_order = $this->sort_order === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_by = $column;
            $this->sort_order = 'asc';
        }
        $this->resetPage();
    }

    public function openForm($id = null)
    {
        if ($id) {
            $class = ClassModel::findOrFail($id);
            $this->editingId = $id;
            $this->form = [
                'name' => $class->name,
                'code' => $class->code,
                'level' => $class->level,
                'section' => $class->section,
                'filiere' => $class->filiere,
                'room_id' => $class->room_id,
                'capacity' => $class->capacity,
                'school_year_id' => $class->school_year_id,
                'description' => $class->description,
            ];
        } else {
            $this->editingId = null;
            $this->form = [
                'name' => '',
                'code' => '',
                'level' => '',
                'section' => '',
                'filiere' => '',
                'room_id' => '',
                'capacity' => 45,
                'school_year_id' => $this->school_year_id,
                'description' => '',
            ];
        }
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->form = [
            'name' => '',
            'code' => '',
            'level' => '',
            'section' => '',
            'filiere' => '',
            'room_id' => '',
            'capacity' => 45,
            'school_year_id' => $this->school_year_id,
            'description' => '',
        ];
    }

    public function saveClass()
    {
        $validated = $this->validate([
            'form.name' => 'required|string|max:100',
            'form.code' => $this->editingId
                ? 'required|string|max:50|unique:classes,code,' . $this->editingId
                : 'required|string|max:50|unique:classes,code',
            'form.level' => 'required|string',
            'form.section' => 'nullable|string|max:10',
            'form.filiere' => 'nullable|string',
            'form.room_id' => 'nullable|exists:rooms,id',
            'form.capacity' => 'required|integer|min:1|max:100',
            'form.school_year_id' => 'required|exists:school_years,id',
            'form.description' => 'nullable|string|max:500',
        ]);

        if ($this->editingId) {
            $class = ClassModel::findOrFail($this->editingId);
            $class->update($this->form);
            $this->dispatch('notify', message: 'Classe mise à jour avec succès', type: 'success');
        } else {
            ClassModel::create($this->form);
            $this->dispatch('notify', message: 'Classe créée avec succès', type: 'success');
        }

        $this->closeForm();
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteConfirm = $id;
    }

    public function deleteClass()
    {
        if ($this->deleteConfirm) {
            ClassModel::findOrFail($this->deleteConfirm)->delete();
            $this->dispatch('notify', message: 'Classe supprimée avec succès', type: 'success');
            $this->deleteConfirm = null;
            $this->resetPage();
        }
    }

    public function cancelDelete()
    {
        $this->deleteConfirm = null;
    }

    public function refreshClasses()
    {
        $this->resetPage();
    }
}
