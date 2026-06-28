<?php

namespace Modules\Attendance\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Attendance\Models\IPBlockList;
use Modules\Audit\Services\AuditService;
use Symfony\Component\HttpFoundation\Response;

class CheckIPBlocklist
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();

        // Check if IP is blocked
        if (IPBlockList::isBlocked($ipAddress)) {
            $reason = IPBlockList::getBlockReason($ipAddress);

            // Log blocked attempt
            $this->auditService->logAction(
                'ip_blocked_attempt',
                'Attendance',
                null,
                null,
                "IP {$ipAddress} blocked. Reason: {$reason}"
            );

            return response()->json([
                'message' => 'Your IP address is blocked from accessing this service',
                'reason' => $reason,
            ], 403)
                ->header('X-IP-Blocked', 'true');
        }

        return $next($request);
    }
}
