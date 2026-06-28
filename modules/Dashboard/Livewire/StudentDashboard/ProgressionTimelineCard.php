<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ProgressionTimelineService;

class ProgressionTimelineCard extends Component
{
    public $timeline = [];
    public $loading = true;

    public function mount(): void
    {
        $service = app(ProgressionTimelineService::class);
        $this->timeline = $service->getProgressionTimeline(6);
        $this->loading = false;
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.progression-timeline-card');
    }
}
