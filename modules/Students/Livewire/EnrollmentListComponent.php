<?php

namespace Modules\Students\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Students\Models\StudentEnrollment;
use Modules\Students\Models\Student;
use Modules\Config\Models\SchoolYear;
use Illuminate\Support\Collection;

class EnrollmentListComponent extends Component
{
    use WithPagination;

    public $filterSchoolYear = '';
    public $filterClass = '';
    public $filterFiliere = '';
    public $filterStatus = '';
    public $filterFromDate = '';
    public $filterToDate = '';
    public $searchQuery = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $selectedEnrollmentId = null;
    public $showDetail = false;
    public $perPage = 25;

    protected $queryString = [
        'filterSchoolYear' => ['except' => ''],
        'filterClass' => ['except' => ''],
        'filterFiliere' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'searchQuery' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', StudentEnrollment::class);
    }

    public function render()
    {
        $query = StudentEnrollment::query()
            ->with(['student', 'class', 'schoolYear']);

        // Apply filters
        if ($this->filterSchoolYear) {
            $query->where('school_year_id', $this->filterSchoolYear);
        }

        if ($this->filterClass) {
            $query->where('class_id', $this->filterClass);
        }

        if ($this->filterFiliere) {
            $query->where('filiere', $this->filterFiliere);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterFromDate) {
            $query->whereDate('enrollment_date', '>=', $this->filterFromDate);
        }

        if ($this->filterToDate) {
            $query->whereDate('enrollment_date', '<=', $this->filterToDate);
        }

        if ($this->searchQuery) {
            $query->whereHas('student', function ($q) {
                $q->where('first_name', 'like', "%{$this->searchQuery}%")
                    ->orWhere('last_name', 'like', "%{$this->searchQuery}%")
                    ->orWhere('student_id_number', 'like', "%{$this->searchQuery}%");
            });
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $enrollments = $query->paginate($this->perPage);

        return view('students::livewire.enrollment-list-component', [
            'enrollments' => $enrollments,
            'schoolYears' => SchoolYear::orderBy('start_year', 'desc')->get(),
            'statuses' => ['active', 'suspended', 'withdrawn', 'graduated'],
        ]);
    }

    public function resetFilters(): void
    {
        $this->reset([
            'filterSchoolYear',
            'filterClass',
            'filterFiliere',
            'filterStatus',
            'filterFromDate',
            'filterToDate',
            'searchQuery',
            'sortBy',
            'sortDirection',
        ]);
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function viewDetail(int $enrollmentId): void
    {
        $this->selectedEnrollmentId = $enrollmentId;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedEnrollmentId = null;
    }

    public function getSelectedEnrollment()
    {
        if (!$this->selectedEnrollmentId) {
            return null;
        }

        return StudentEnrollment::with(['student', 'class', 'schoolYear'])
            ->find($this->selectedEnrollmentId);
    }

    public function deleteEnrollment(int $enrollmentId): void
    {
        $enrollment = StudentEnrollment::findOrFail($enrollmentId);
        $this->authorize('delete', $enrollment);

        try {
            $enrollment->delete();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => trans('students.messages.enrollment_deleted'),
            ]);
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => trans('students.errors.enrollment_deletion_failed'),
            ]);
        }
    }

    public function suspendEnrollment(int $enrollmentId): void
    {
        $enrollment = StudentEnrollment::findOrFail($enrollmentId);
        $this->authorize('manageStatus', $enrollment);

        try {
            $enrollment->update(['status' => 'suspended']);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => trans('students.messages.enrollment_suspended'),
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => trans('students.errors.enrollment_suspension_failed'),
            ]);
        }
    }

    public function resumeEnrollment(int $enrollmentId): void
    {
        $enrollment = StudentEnrollment::findOrFail($enrollmentId);
        $this->authorize('manageStatus', $enrollment);

        try {
            $enrollment->update(['status' => 'active']);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => trans('students.messages.enrollment_resumed'),
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => trans('students.errors.enrollment_resumption_failed'),
            ]);
        }
    }

    public function exportEnrollments(): void
    {
        $this->authorize('export', StudentEnrollment::class);

        $query = StudentEnrollment::query()->with('student');

        if ($this->filterSchoolYear) {
            $query->where('school_year_id', $this->filterSchoolYear);
        }

        if ($this->filterClass) {
            $query->where('class_id', $this->filterClass);
        }

        if ($this->filterFiliere) {
            $query->where('filiere', $this->filterFiliere);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $enrollments = $query->get();

        $csv = "Matricule,Nom,Prénom,Classe,Filière,Niveau,Année,Statut,Date Inscription\n";
        foreach ($enrollments as $enrollment) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $enrollment->student->student_id_number,
                $enrollment->student->last_name,
                $enrollment->student->first_name,
                $enrollment->class_id ?? 'N/A',
                $enrollment->filiere ?? 'N/A',
                $enrollment->level ?? 'N/A',
                $enrollment->schoolYear?->name ?? 'N/A',
                $enrollment->status,
                $enrollment->enrollment_date->format('d/m/Y')
            );
        }

        $filename = 'enrollments_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename);
    }

    public function filterToday(): void
    {
        $this->filterFromDate = now()->toDateString();
        $this->filterToDate = now()->toDateString();
        $this->resetPage();
    }

    public function filterThisWeek(): void
    {
        $this->filterFromDate = now()->startOfWeek()->toDateString();
        $this->filterToDate = now()->endOfWeek()->toDateString();
        $this->resetPage();
    }

    public function filterThisMonth(): void
    {
        $this->filterFromDate = now()->startOfMonth()->toDateString();
        $this->filterToDate = now()->endOfMonth()->toDateString();
        $this->resetPage();
    }

    public function filterActiveOnly(): void
    {
        $this->filterStatus = 'active';
        $this->resetPage();
    }

    public function filterSuspendedOnly(): void
    {
        $this->filterStatus = 'suspended';
        $this->resetPage();
    }
}
