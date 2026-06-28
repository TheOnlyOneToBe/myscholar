<?php

namespace Modules\Teachers\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Modules\Teachers\Models\TeacherApplication;

#[Layout('layouts.app')]
class TeacherApplicationReview extends Component
{
    use WithPagination;

    public $filter = 'pending';
    public $search = '';
    public $filiere = '';
    public $selectedApplication = null;
    public $rejectionReason = '';
    public $showRejectModal = false;
    public $perPage = 10;

    protected $queryString = ['filter', 'search', 'filiere'];

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFiliere()
    {
        $this->resetPage();
    }

    public function getApplicationsProperty()
    {
        $query = TeacherApplication::with(['user']);

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($subQuery) {
                    $subQuery->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })
                ->orWhere('specialization', 'like', "%{$this->search}%")
                ->orWhere('teacher_code', 'like', "%{$this->search}%");
            });
        }

        if ($this->filiere) {
            $query->where('filiere', $this->filiere);
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    public function selectApplication($applicationId)
    {
        $this->selectedApplication = TeacherApplication::find($applicationId);
    }

    public function approveApplication($applicationId)
    {
        $application = TeacherApplication::findOrFail($applicationId);

        if (!$application->isPending()) {
            $this->dispatch('error', 'Cette candidature a déjà été traitée.');
            return;
        }

        $application->approve(auth()->user());

        $this->selectedApplication = null;
        $this->dispatch('success', 'Candidature approuvée! L\'enseignant a été enregistré.');
        $this->reset('filter');
    }

    public function openRejectModal($applicationId)
    {
        $this->selectedApplication = TeacherApplication::find($applicationId);
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function rejectApplication($applicationId)
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:500',
        ], [
            'rejectionReason.required' => 'Veuillez fournir une raison pour le rejet.',
        ]);

        $application = TeacherApplication::findOrFail($applicationId);

        if (!$application->isPending()) {
            $this->dispatch('error', 'Cette candidature a déjà été traitée.');
            return;
        }

        $application->reject($this->rejectionReason);

        $this->selectedApplication = null;
        $this->showRejectModal = false;
        $this->rejectionReason = '';
        $this->dispatch('success', 'Candidature rejetée.');
        $this->reset('filter');
    }

    public function render()
    {
        return view('teachers::livewire.teacher-application-review', [
            'applications' => $this->applications,
        ]);
    }
}
