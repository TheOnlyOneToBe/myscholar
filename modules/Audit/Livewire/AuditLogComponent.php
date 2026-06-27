<?php

namespace Modules\Audit\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Modules\Audit\Models\AuditLog;
use Modules\Auth\Models\User;
use Illuminate\Support\Carbon;

#[Layout('audit::app')]
class AuditLogComponent extends Component
{
    use WithPagination;

    // Filters
    #[Validate('nullable|string|in:create,read,update,delete,export,import,login,logout,auth_failed,permission_denied,error,crash,system_event')]
    public ?string $filterAction = null;

    #[Validate('nullable|integer|exists:users,id')]
    public ?int $filterUser = null;

    #[Validate('nullable|string|in:info,warning,error,critical')]
    public ?string $filterSeverity = null;

    #[Validate('nullable|date')]
    public ?string $filterFromDate = null;

    #[Validate('nullable|date')]
    public ?string $filterToDate = null;

    #[Validate('nullable|string|max:255')]
    public ?string $filterEntityType = null;

    // UI State
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public string $searchQuery = '';
    public ?int $selectedLogId = null;
    public bool $showDetail = false;

    public function mount()
    {
        $this->authorize('audit.view', AuditLog::class);
    }

    public function render()
    {
        $query = AuditLog::with('user');

        // Apply filters
        if ($this->filterAction) {
            $query->byAction($this->filterAction);
        }

        if ($this->filterUser) {
            $query->byUser($this->filterUser);
        }

        if ($this->filterSeverity) {
            $query->bySeverity($this->filterSeverity);
        }

        if ($this->filterFromDate && $this->filterToDate) {
            $from = Carbon::createFromFormat('Y-m-d', $this->filterFromDate)->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d', $this->filterToDate)->endOfDay();
            $query->dateRange($from, $to);
        }

        if ($this->filterEntityType) {
            $query->byEntityType($this->filterEntityType);
        }

        if ($this->searchQuery) {
            $query->where(function ($q) {
                $q->where('entity_type', 'like', "%{$this->searchQuery}%")
                    ->orWhere('action', 'like', "%{$this->searchQuery}%")
                    ->orWhere('url', 'like', "%{$this->searchQuery}%")
                    ->orWhere('error_message', 'like', "%{$this->searchQuery}%")
                    ->orWhere('ip_address', 'like', "%{$this->searchQuery}%");
            });
        }

        // Apply sorting
        if (in_array($this->sortBy, ['created_at', 'action', 'severity', 'http_status'])) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $logs = $query->paginate(25);
        $users = User::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']);
        $actions = AuditLog::getActions();
        $severities = AuditLog::getSeverityLevels();
        $entityTypes = array_keys(__('audit.entity_types'));

        return view('audit::livewire.audit-log-component', [
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'severities' => $severities,
            'entityTypes' => $entityTypes,
        ]);
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->reset(['filterAction', 'filterUser', 'filterSeverity', 'filterFromDate', 'filterToDate', 'filterEntityType', 'searchQuery']);
        $this->resetPage();
    }

    /**
     * View audit log detail
     */
    public function viewDetail(int $logId)
    {
        $log = AuditLog::findOrFail($logId);
        $this->authorize('audit.view', $log);

        $this->selectedLogId = $logId;
        $this->showDetail = true;
    }

    /**
     * Close detail view
     */
    public function closeDetail()
    {
        $this->selectedLogId = null;
        $this->showDetail = false;
    }

    /**
     * Change sort column and direction
     */
    public function sortBy(string $column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Quick filter: Show only today's logs
     */
    public function filterToday()
    {
        $today = now()->format('Y-m-d');
        $this->filterFromDate = $today;
        $this->filterToDate = $today;
        $this->resetPage();
    }

    /**
     * Quick filter: Show only this week's logs
     */
    public function filterThisWeek()
    {
        $this->filterFromDate = now()->startOfWeek()->format('Y-m-d');
        $this->filterToDate = now()->endOfWeek()->format('Y-m-d');
        $this->resetPage();
    }

    /**
     * Quick filter: Show only this month's logs
     */
    public function filterThisMonth()
    {
        $this->filterFromDate = now()->startOfMonth()->format('Y-m-d');
        $this->filterToDate = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    /**
     * Quick filter: Show only errors
     */
    public function filterErrorsOnly()
    {
        $this->filterSeverity = 'error';
        $this->resetPage();
    }

    /**
     * Quick filter: Show only critical errors
     */
    public function filterCriticalOnly()
    {
        $this->filterSeverity = 'critical';
        $this->resetPage();
    }

    /**
     * Get selected log with detail
     */
    public function getSelectedLog()
    {
        if (!$this->selectedLogId) {
            return null;
        }

        return AuditLog::with('user')->findOrFail($this->selectedLogId);
    }

    /**
     * Delete audit log (admin only)
     */
    public function deleteLog(int $logId)
    {
        $log = AuditLog::findOrFail($logId);
        $this->authorize('audit.delete', $log);

        $log->delete();
        $this->dispatch('notify', type: 'success', message: __('audit.messages.delete_successful'));
    }

    /**
     * Export filtered logs as CSV
     */
    public function exportLogs()
    {
        $this->authorize('audit.export', AuditLog::class);

        $query = AuditLog::with('user');

        if ($this->filterAction) {
            $query->byAction($this->filterAction);
        }
        if ($this->filterUser) {
            $query->byUser($this->filterUser);
        }
        if ($this->filterSeverity) {
            $query->bySeverity($this->filterSeverity);
        }
        if ($this->filterFromDate && $this->filterToDate) {
            $from = Carbon::createFromFormat('Y-m-d', $this->filterFromDate)->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d', $this->filterToDate)->endOfDay();
            $query->dateRange($from, $to);
        }

        $logs = $query->get();

        $csv = "Timestamp,User,Action,Entity Type,Entity ID,Severity,IP Address,URL,Status,Error Message\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user?->first_name . ' ' . $log->user?->last_name,
                $log->action,
                $log->entity_type,
                $log->entity_id,
                $log->severity,
                $log->ip_address,
                $log->url,
                $log->http_status ?? 'N/A',
                str_replace('"', '""', $log->error_message ?? '')
            );
        }

        return response()->streamDownload(
            fn () => print $csv,
            'audit-logs-' . now()->format('Y-m-d-His') . '.csv'
        );
    }
}
