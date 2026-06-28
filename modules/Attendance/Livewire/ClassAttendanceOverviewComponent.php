<?php

namespace Modules\Attendance\Livewire;

use Livewire\Component;
use Modules\Attendance\Services\AttendanceService;
use Modules\Classes\Models\Classes;

class ClassAttendanceOverviewComponent extends Component
{
    public $classId;
    public $class;
    public $selectedDate;
    public $overview = null;

    protected $attendanceService;

    public function mount($classId)
    {
        $this->classId = $classId;
        $this->class = Classes::find($classId);
        $this->selectedDate = now()->format('Y-m-d');
        $this->attendanceService = app(AttendanceService::class);

        $this->loadOverview();
    }

    public function render()
    {
        return view('attendance::livewire.class-attendance-overview', [
            'overview' => $this->overview,
            'class' => $this->class,
        ]);
    }

    public function loadOverview()
    {
        if ($this->class) {
            $this->overview = $this->attendanceService->getClassAttendanceOverview(
                $this->classId,
                $this->selectedDate
            );
        }
    }

    public function updatedSelectedDate()
    {
        $this->loadOverview();
    }

    public function previousDay()
    {
        $this->selectedDate = date('Y-m-d', strtotime($this->selectedDate . ' -1 day'));
        $this->loadOverview();
    }

    public function nextDay()
    {
        $this->selectedDate = date('Y-m-d', strtotime($this->selectedDate . ' +1 day'));
        $this->loadOverview();
    }

    public function today()
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadOverview();
    }
}
