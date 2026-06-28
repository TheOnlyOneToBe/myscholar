<?php

namespace Modules\Teachers\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Teachers\Models\TeacherApplication;
use Illuminate\Pagination\Paginator;

#[Layout('layouts.app')]
class TeacherApplicationReview extends Component
{
    public $filter = 'pending';
    public $selectedApplication = null;
    public $rejectionReason = '';
    public $showRejectModal = false;

    public function getApplicationsProperty()
    {
        $query = TeacherApplication::with(['user']);

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
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
