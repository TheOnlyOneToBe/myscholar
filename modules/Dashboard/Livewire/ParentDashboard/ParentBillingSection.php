<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\ParentDashboardService;

class ParentBillingSection extends Component
{
    public $selectedChildId = null;
    public $outstandingInvoices = [];
    public $recentPayments = [];
    public $outstandingBalance = 0;
    public $childName = '';

    protected $listeners = ['childSelected' => 'updateSelectedChild'];

    public function mount(?int $childId = null): void
    {
        if ($childId) {
            $this->selectedChildId = $childId;
        }
        $this->loadBillingData();
    }

    public function updateSelectedChild(int $childId): void
    {
        $this->selectedChildId = $childId;
        $this->loadBillingData();
    }

    private function loadBillingData(): void
    {
        if (!$this->selectedChildId) {
            return;
        }

        $user = auth()->user();

        if (!$user || !$user->hasRole('parent')) {
            return;
        }

        try {
            $service = app(ParentDashboardService::class);

            $this->outstandingInvoices = $service->getChildOutstandingInvoices($this->selectedChildId);
            $this->recentPayments = $service->getChildRecentPayments($this->selectedChildId);
            $this->outstandingBalance = $service->getChildOutstandingBalance($this->selectedChildId);

            $children = $service->getChildren();
            $child = collect($children)->firstWhere('id', $this->selectedChildId);
            $this->childName = $child['full_name'] ?? 'Étudiant';

        } catch (\Exception $e) {
            \Log::error('Error loading parent billing: ' . $e->getMessage());
        }
    }

    public function downloadInvoice(string $invoiceId): void
    {
        if (!$this->selectedChildId) {
            $this->dispatch('error', 'No child selected');
            return;
        }

        try {
            $redirectUrl = route('dashboard.documents.invoice', [
                'invoiceId' => $invoiceId,
            ]) . '?student_id=' . $this->selectedChildId;

            $this->redirect($redirectUrl);
        } catch (\Exception $e) {
            \Log::error('Error downloading invoice: ' . $e->getMessage());
            $this->dispatch('error', 'Error downloading invoice');
        }
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-billing-section');
    }
}
