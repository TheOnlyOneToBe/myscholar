<?php

namespace Modules\Audit\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Audit\Models\AuditLog;
use Modules\Audit\Services\AuditService;

class AuditLogController extends Controller
{
    public function __construct(protected AuditService $auditService) {}

    /**
     * Get all audit logs with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('audit.view')) {
            return response()->json(['message' => __('audit.errors.permission_denied')], 403);
        }

        $query = AuditLog::query();

        // Filter by action
        if ($request->has('action')) {
            $query->byAction($request->input('action'));
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->byUser($request->input('user_id'));
        }

        // Filter by entity
        if ($request->has('entity_type')) {
            $query->byEntityType($request->input('entity_type'));
            if ($request->has('entity_id')) {
                $query->where('entity_id', $request->input('entity_id'));
            }
        }

        // Filter by severity
        if ($request->has('severity')) {
            $query->bySeverity($request->input('severity'));
        }

        // Filter by date range
        if ($request->has('from') && $request->has('to')) {
            $query->dateRange($request->input('from'), $request->input('to'));
        } elseif ($request->has('hours')) {
            $query->recent($request->input('hours', 24));
        }

        // Filter by URL/route
        if ($request->has('url')) {
            $query->byRoute($request->input('url'));
        }

        // Filter errors only
        if ($request->boolean('errors_only')) {
            $query->errors();
        }

        // Filter HTTP errors only
        if ($request->boolean('http_errors_only')) {
            $query->httpErrors();
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 50));

        return response()->json($logs);
    }

    /**
     * Get a specific audit log
     */
    public function show(AuditLog $log): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('audit.view')) {
            return response()->json(['message' => __('audit.errors.permission_denied')], 403);
        }

        return response()->json(['data' => $log]);
    }

    /**
     * Get recent errors dashboard
     */
    public function recentErrors(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('audit.view_errors')) {
            return response()->json(['message' => __('audit.errors.permission_denied')], 403);
        }

        $hours = $request->input('hours', 24);
        $limit = $request->input('limit', 100);

        return response()->json([
            'errors' => $this->auditService->getRecentErrors($hours, $limit),
        ]);
    }

    /**
     * Get crash logs
     */
    public function crashes(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('audit.view_errors')) {
            return response()->json(['message' => __('audit.errors.permission_denied')], 403);
        }

        $limit = $request->input('limit', 50);

        return response()->json([
            'crashes' => $this->auditService->getCrashLogs($limit),
        ]);
    }

    /**
     * Get failed requests
     */
    public function failedRequests(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('audit.view_errors')) {
            return response()->json(['message' => __('audit.errors.permission_denied')], 403);
        }

        $hours = $request->input('hours', 24);
        $limit = $request->input('limit', 100);

        return response()->json([
            'failed_requests' => $this->auditService->getFailedRequests($hours, $limit),
        ]);
    }

    /**
     * Get audit statistics dashboard
     */
    public function stats(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('audit.view_stats')) {
            return response()->json(['message' => __('audit.errors.permission_denied')], 403);
        }

        $days = $request->input('days', 30);

        return response()->json([
            'suspicious_activity' => $this->auditService->getSuspiciousActivity(24),
            'error_stats' => $this->auditService->getErrorStats($days),
        ]);
    }

    /**
     * Get user activity log
     */
    public function userActivity(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('audit.view')) {
            return response()->json(['message' => __('audit.errors.permission_denied')], 403);
        }

        $userId = $request->input('user_id', $user->id);
        $limit = $request->input('limit', 50);

        $logs = AuditLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['activities' => $logs]);
    }

    /**
     * Export audit logs
     */
    public function export(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        if (!$user || !$user->hasPermission('audit.export')) {
            return response()->json(['message' => __('audit.errors.permission_denied')], 403);
        }

        $query = AuditLog::query();

        if ($request->has('from') && $request->has('to')) {
            $query->dateRange($request->input('from'), $request->input('to'));
        }

        if ($request->has('action')) {
            $query->byAction($request->input('action'));
        }

        $logs = $query->get();

        return response()->json([
            'total' => $logs->count(),
            'data' => $logs,
        ]);
    }
}
