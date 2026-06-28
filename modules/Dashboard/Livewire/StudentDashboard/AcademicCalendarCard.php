<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\AcademicCalendarService;

class AcademicCalendarCard extends Component
{
    public $calendar = [];
    public $loading = true;

    public function mount(): void
    {
        $service = app(AcademicCalendarService::class);
        $this->calendar = $service->getAcademicCalendar();
        $this->loading = false;
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.academic-calendar-card');
    }
}
