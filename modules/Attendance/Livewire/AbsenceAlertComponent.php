<?php

namespace Modules\Attendance\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Attendance\Repositories\AbsenceRepository;
use Modules\Attendance\Models\AbsenceAlert;

class AbsenceAlertComponent extends Component
{
    use WithPagination;

    public $perPage = 25;
    public $showPendingOnly = true;

    protected $absenceRepository;

    public function mount()
    {
        $this->absenceRepository = app(AbsenceRepository::class);
    }

    public function render()
    {
        if ($this->showPendingOnly) {
            $alerts = $this->absenceRepository->getPendingAlerts($this->perPage);
        } else {
            $alerts = AbsenceAlert::query()
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage);
        }

        return view('attendance::livewire.absence-alert', [
            'alerts' => $alerts,
        ]);
    }

    public function acknowledgeAlert(AbsenceAlert $alert)
    {
        try {
            $this->absenceRepository->acknowledgeAlert($alert);
            session()->flash('message', 'Alert acknowledged successfully');
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to acknowledge alert');
        }
    }

    public function togglePendingFilter()
    {
        $this->showPendingOnly = !$this->showPendingOnly;
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
