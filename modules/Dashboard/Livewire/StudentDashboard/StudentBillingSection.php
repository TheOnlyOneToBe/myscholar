<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use App\Services\ModuleManager;

class StudentBillingSection extends Component
{
    public $upcomingPayments = [];
    public $outstandingBalance = 0;
    public $overdueCount = 0;
    public $moduleAvailable = false;
    public $moduleError = '';

    public function mount(): void
    {
        $this->checkModuleAvailability();
    }

    private function checkModuleAvailability(): void
    {
        $moduleManager = app(ModuleManager::class);

        if (!$moduleManager->canUseModule('Billing')) {
            $this->moduleAvailable = false;
            $this->moduleError = $moduleManager->getModuleError('Billing');
            return;
        }

        $user = auth()->user();
        if (!$user || !$user->hasRole('student')) {
            $this->moduleAvailable = false;
            $this->moduleError = 'You do not have permission to view billing';
            return;
        }

        $this->moduleAvailable = true;
        $this->loadBillingData();
    }

    private function loadBillingData(): void
    {
        try {
            $service = app(StudentDashboardService::class);
            $stats = $service->getQuickStats();

            $this->upcomingPayments = $service->getUpcomingPaymentsDue(3);
            $this->outstandingBalance = $stats['outstanding_balance'] ?? 0;
            $this->overdueCount = $stats['overdue_invoices'] ?? 0;
        } catch (\Exception $e) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Error loading billing: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-billing-section');
    }
}
