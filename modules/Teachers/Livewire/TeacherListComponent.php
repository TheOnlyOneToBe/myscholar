<?php

namespace Modules\Teachers\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Modules\Teachers\Models\Teacher;

#[Layout('layouts.app')]
class TeacherListComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $filiere = '';
    public $specialization = '';
    public $isActive = true;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    protected $queryString = ['search', 'filiere', 'specialization', 'isActive', 'sortBy', 'sortDirection'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFiliere()
    {
        $this->resetPage();
    }

    public function updatedSpecialization()
    {
        $this->resetPage();
    }

    public function updatedIsActive()
    {
        $this->resetPage();
    }

    public function getTeachersProperty()
    {
        $query = Teacher::with(['user', 'subjects', 'classes']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($subQuery) {
                    $subQuery->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })
                ->orWhere('teacher_code', 'like', "%{$this->search}%")
                ->orWhere('specialization', 'like', "%{$this->search}%");
            });
        }

        // Filter by filière
        if ($this->filiere) {
            $query->where('filiere', $this->filiere);
        }

        // Filter by specialization
        if ($this->specialization) {
            $query->where('specialization', $this->specialization);
        }

        // Filter by active status
        if ($this->isActive !== '') {
            $query->where('is_active', (bool) $this->isActive);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function setSortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filiere = '';
        $this->specialization = '';
        $this->isActive = true;
        $this->resetPage();
    }

    public function render()
    {
        return view('teachers::livewire.teacher-list', [
            'teachers' => $this->teachers,
        ]);
    }
}
