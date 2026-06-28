<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ParentDashboardService;

class ParentAlertsSection extends Component
{
    public $alerts = [];

    public function mount(): void
    {
        $this->loadAlerts();
    }

    private function loadAlerts(): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('parent')) {
            return;
        }

        try {
            $service = app(ParentDashboardService::class);
            $this->alerts = $service->getAlerts();

        } catch (\Exception $e) {
            \Log::error('Error loading parent alerts: ' . $e->getMessage());
        }
    }

    public function refreshAlerts(): void
    {
        $this->loadAlerts();
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-alerts-section');
    }
}
