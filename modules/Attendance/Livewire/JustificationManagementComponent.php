<?php

namespace Modules\Attendance\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Attendance\Repositories\JustificationRepository;
use Modules\Attendance\Services\JustificationService;
use Modules\Attendance\Models\Justification;

class JustificationManagementComponent extends Component
{
    use WithPagination;

    public $statusFilter = 'pending';
    public $perPage = 25;
    public $showResponseModal = false;
    public $selectedJustification = null;
    public $responseText = '';

    protected $justificationRepository;
    protected $justificationService;

    public function mount()
    {
        $this->justificationRepository = app(JustificationRepository::class);
        $this->justificationService = app(JustificationService::class);
    }

    public function render()
    {
        if ($this->statusFilter === 'pending') {
            $justifications = $this->justificationRepository->findPending($this->perPage);
        } else {
            $justifications = $this->justificationRepository->findByStatus($this->statusFilter, $this->perPage);
        }

        return view('attendance::livewire.justification-management', [
            'justifications' => $justifications,
        ]);
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function openResponseModal(Justification $justification)
    {
        $this->selectedJustification = $justification;
        $this->showResponseModal = true;
    }

    public function closeModal()
    {
        $this->showResponseModal = false;
        $this->selectedJustification = null;
        $this->responseText = '';
    }

    public function approve(Justification $justification)
    {
        try {
            $this->justificationService->approveJustification($justification);
            session()->flash('message', 'Justification approved successfully');
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve justification');
        }
    }

    public function reject()
    {
        if (!$this->selectedJustification || !$this->responseText) {
            session()->flash('error', 'Rejection reason is required');
            return;
        }

        try {
            $this->justificationService->rejectJustification(
                $this->selectedJustification,
                $this->responseText
            );
            session()->flash('message', 'Justification rejected successfully');
            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject justification');
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
