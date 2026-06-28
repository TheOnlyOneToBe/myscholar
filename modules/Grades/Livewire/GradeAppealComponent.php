<?php

namespace Modules\Grades\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Grades\Models\GradeAppeal;
use Modules\Grades\Services\GradeAppealService;

class GradeAppealComponent extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $perPage = 25;
    public $showModal = false;
    public $reviewingId = null;
    public $reviewData = [
        'response' => '',
        'status' => 'pending',
    ];

    protected $paginationTheme = 'tailwind';

    public function openReviewModal($id, $status)
    {
        $this->reviewingId = $id;
        $this->reviewData['status'] = $status;
        $this->showModal = true;
    }

    public function submitReview()
    {
        $this->validate([
            'reviewData.response' => 'required|string|min:10|max:1000',
            'reviewData.status' => 'required|in:approved,rejected',
        ]);

        $service = app(GradeAppealService::class);

        if ($this->reviewData['status'] === 'approved') {
            $service->approveAppeal($this->reviewingId, auth()->id(), $this->reviewData['response']);
        } else {
            $service->rejectAppeal($this->reviewingId, auth()->id(), $this->reviewData['response']);
        }

        $this->dispatch('notify', ['message' => "Appeal {$this->reviewData['status']} successfully"]);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reviewingId = null;
        $this->reviewData = [
            'response' => '',
            'status' => 'pending',
        ];
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = GradeAppeal::query();

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $appeals = $query->with(['student', 'grade', 'subject', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('grades::livewire.grade-appeal', ['appeals' => $appeals]);
    }
}
