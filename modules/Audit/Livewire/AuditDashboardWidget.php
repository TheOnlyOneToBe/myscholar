<?php

namespace Modules\Audit\Livewire;

use Livewire\Component;
use Modules\Audit\Models\AuditLog;
use Illuminate\Support\Carbon;

class AuditDashboardWidget extends Component
{
    public int $totalLogs = 0;
    public int $todayLogs = 0;
    public int $recentErrors = 0;
    public int $criticalErrors = 0;
    public int $failedRequests = 0;
    public float $errorRate = 0;
    public array $topErrors = [];
    public array $mostActiveUsers = [];
    public array $mostAccessedRoutes = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // Total logs
        $this->totalLogs = AuditLog::count();

        // Today's logs
        $this->todayLogs = AuditLog::whereDate('created_at', today())->count();

        // Recent errors (last 24 hours)
        $this->recentErrors = AuditLog::errors()
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        // Critical errors
        $this->criticalErrors = AuditLog::bySeverity('critical')->count();

        // Failed requests (HTTP 4xx, 5xx)
        $this->failedRequests = AuditLog::httpErrors()->count();

        // Error rate
        $totalRequests = AuditLog::whereNotNull('http_status')->count();
        $this->errorRate = $totalRequests > 0
            ? round(($this->failedRequests / $totalRequests) * 100, 2)
            : 0;

        // Top errors
        $this->topErrors = AuditLog::select('error_message')
            ->whereNotNull('error_message')
            ->groupBy('error_message')
            ->selectRaw('COUNT(*) as count')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn($log) => [
                'message' => substr($log->error_message, 0, 60) . (strlen($log->error_message) > 60 ? '...' : ''),
                'count' => $log->count,
            ])
            ->toArray();

        // Most active users
        $this->mostActiveUsers = AuditLog::with('user')
            ->whereNotNull('user_id')
            ->selectRaw('user_id, COUNT(*) as activity_count')
            ->groupBy('user_id')
            ->orderByDesc('activity_count')
            ->limit(5)
            ->get()
            ->map(fn($log) => [
                'user' => $log->user?->first_name . ' ' . $log->user?->last_name,
                'count' => $log->activity_count,
            ])
            ->toArray();

        // Most accessed routes
        $this->mostAccessedRoutes = AuditLog::select('url')
            ->whereNotNull('url')
            ->selectRaw('COUNT(*) as access_count')
            ->groupBy('url')
            ->orderByDesc('access_count')
            ->limit(5)
            ->get()
            ->map(fn($log) => [
                'url' => substr($log->url, 0, 50) . (strlen($log->url) > 50 ? '...' : ''),
                'count' => $log->access_count,
            ])
            ->toArray();
    }

    public function refresh()
    {
        $this->loadDashboardData();
    }

    public function render()
    {
        return view('audit::livewire.audit-dashboard-widget');
    }
}
