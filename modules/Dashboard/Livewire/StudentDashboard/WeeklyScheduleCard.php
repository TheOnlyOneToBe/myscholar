<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\WeeklyScheduleService;

class WeeklyScheduleCard extends Component
{
    public $schedule = [];
    public $loading = true;
    public $activeDay = 'today';

    public function mount(): void
    {
        $service = app(WeeklyScheduleService::class);
        $this->schedule = $service->getWeeklySchedule();
        $this->loading = false;
    }

    public function switchDay($day): void
    {
        $this->activeDay = $day;
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.weekly-schedule-card');
    }
}
