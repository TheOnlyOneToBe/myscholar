<?php

namespace Modules\Audit\Services;

use Modules\Audit\Models\AuditLog;
use Modules\Audit\Models\DeletedRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditService
{
    /**
     * Log an action
     */
    public function logAction(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $changes = null,
        ?string $description = null
    ): AuditLog {
        return AuditLog::create([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => Auth::id(),
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => request()->method(),
            'url' => request()->url(),
            'http_status' => null,
            'severity' => 'info',
            'metadata' => ['description' => $description],
        ]);
    }

    /**
     * Log an HTTP request/response
     */
    public function logRequest(
        string $method,
        string $url,
        int $status,
        ?float $duration = null,
        ?array $metadata = null
    ): AuditLog {
        $severity = $status >= 500 ? 'critical' : ($status >= 400 ? 'error' : 'info');

        return AuditLog::create([
            'action' => 'http_request',
            'entity_type' => 'Route',
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => $method,
            'url' => $url,
            'http_status' => $status,
            'severity' => $severity,
            'metadata' => array_merge(['duration_ms' => $duration], $metadata ?? []),
        ]);
    }

    /**
     * Log an error/exception
     */
    public function logError(
        \Exception $exception,
        string $action = 'error',
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $context = null
    ): AuditLog {
        $severity = method_exists($exception, 'getStatusCode') && $exception->getStatusCode() >= 500
            ? 'critical'
            : 'error';

        return AuditLog::create([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => request()->method(),
            'url' => request()->url(),
            'http_status' => method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : null,
            'error_message' => $exception->getMessage(),
            'stack_trace' => $exception->getTraceAsString(),
            'severity' => $severity,
            'metadata' => $context,
        ]);
    }

    /**
     * Log a crash/fatal error
     */
    public function logCrash(
        string $message,
        string $stackTrace,
        ?array $context = null
    ): AuditLog {
        return AuditLog::create([
            'action' => 'crash',
            'entity_type' => 'System',
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => request()->method(),
            'url' => request()->url(),
            'error_message' => $message,
            'stack_trace' => $stackTrace,
            'severity' => 'critical',
            'metadata' => $context,
        ]);
    }

    /**
     * Log an authentication action
     */
    public function logAuth(string $action, $user, string $ipAddress, ?string $reason = null): AuditLog
    {
        return AuditLog::create([
            'action' => $action, // login, logout, login_failed, etc.
            'entity_type' => 'Auth',
            'user_id' => $user?->id,
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
            'method' => 'POST',
            'url' => request()->url(),
            'severity' => in_array($action, ['login_failed', 'auth_failed']) ? 'warning' : 'info',
            'metadata' => ['reason' => $reason],
        ]);
    }

    /**
     * Log a permission denied event
     */
    public function logPermissionDenied(
        string $permission,
        ?string $entityType = null,
        ?int $entityId = null
    ): AuditLog {
        return AuditLog::create([
            'action' => 'permission_denied',
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => request()->method(),
            'url' => request()->url(),
            'severity' => 'warning',
            'metadata' => ['permission' => $permission],
        ]);
    }

    /**
     * Record a deleted model for audit trail
     */
    public function recordDeletedModel($model, ?string $reason = null): DeletedRecord
    {
        return DeletedRecord::create([
            'model_class' => get_class($model),
            'table_name' => $model->getTable(),
            'record_id' => $model->getKey(),
            'data' => $model->toArray(),
            'deleted_by_user_id' => Auth::id(),
            'deletion_reason' => $reason,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Get recent errors for monitoring dashboard
     */
    public function getRecentErrors(int $hours = 24, int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::errors()
            ->recent($hours)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get crash logs for monitoring
     */
    public function getCrashLogs(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('action', 'crash')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get failed requests
     */
    public function getFailedRequests(int $hours = 24, int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('action', 'http_request')
            ->httpErrors()
            ->recent($hours)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get suspicious activity (failed logins, permission denied, etc.)
     */
    public function getSuspiciousActivity(int $hours = 24): array
    {
        return [
            'failed_logins' => AuditLog::where('action', 'login_failed')
                ->recent($hours)
                ->count(),
            'permission_denied' => AuditLog::where('action', 'permission_denied')
                ->recent($hours)
                ->count(),
            'errors' => AuditLog::errors()
                ->recent($hours)
                ->count(),
            'crashed' => AuditLog::where('action', 'crash')
                ->recent($hours)
                ->count(),
        ];
    }

    /**
     * Get error statistics
     */
    public function getErrorStats(int $days = 30): array
    {
        $logs = AuditLog::errors()
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        return [
            'total_errors' => $logs->count(),
            'by_severity' => $logs->groupBy('severity')->map->count(),
            'by_action' => $logs->groupBy('action')->map->count(),
            'by_entity' => $logs->groupBy('entity_type')->map->count(),
            'critical_count' => $logs->where('severity', 'critical')->count(),
        ];
    }
}
