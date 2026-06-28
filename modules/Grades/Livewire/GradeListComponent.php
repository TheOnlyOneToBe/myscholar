<?php

namespace Modules\Grades\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Grades\Models\Grade;
use Modules\Grades\Models\Subject;
use Modules\Grades\Models\GradePeriod;

class GradeListComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $filterSubject = '';
    public $filterPeriod = '';
    public $filterType = '';
    public $perPage = 25;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->paginationTheme = 'tailwind';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterSubject()
    {
        $this->resetPage();
    }

    public function updatingFilterPeriod()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Grade::with(['student', 'subject', 'teacher', 'gradePeriod']);

        if ($this->search) {
            $query->whereHas('student', function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('student_id_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterSubject) {
            $query->where('subject_id', $this->filterSubject);
        }

        if ($this->filterPeriod) {
            $query->where('grade_period_id', $this->filterPeriod);
        }

        if ($this->filterType) {
            $query->where('grade_type', $this->filterType);
        }

        $grades = $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('grades::livewire.grade-list', [
            'grades' => $grades,
            'subjects' => Subject::active()->get(),
            'periods' => GradePeriod::all(),
        ]);
    }
}
