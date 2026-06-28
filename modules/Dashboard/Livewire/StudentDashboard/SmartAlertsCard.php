<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\SmartAlertsService;

class SmartAlertsCard extends Component
{
    public $alerts = [];
    public $loading = true;
    public $dismissedAlerts = [];

    public function mount(): void
    {
        $service = app(SmartAlertsService::class);
        $this->alerts = $service->getSmartAlerts();
        $this->loading = false;
    }

    public function dismissAlert($alertId): void
    {
        $this->dismissedAlerts[] = $alertId;
        $this->alerts['alerts'] = array_filter(
            $this->alerts['alerts'],
            fn($alert) => !in_array($alert['id'], $this->dismissedAlerts)
        );
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.smart-alerts-card');
    }
}
