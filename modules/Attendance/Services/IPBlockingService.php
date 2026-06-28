<?php

namespace Modules\Attendance\Services;

use Modules\Attendance\Models\IPBlockList;
use Modules\Audit\Services\AuditService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class IPBlockingService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Track rate limit violations
     * Auto-block IP after threshold violations
     */
    public function trackRateLimitViolation(string $ipAddress, string $endpoint = 'general'): void
    {
        $cacheKey = "rate_limit_violations:{$ipAddress}:{$endpoint}";
        $violations = (int) Cache::get($cacheKey, 0);
        $violations++;

        // Store violations for 1 hour
        Cache::put($cacheKey, $violations, 3600);

        // Log the violation
        $this->auditService->logAction(
            'rate_limit_violation',
            'Attendance',
            null,
            [
                'ip_address' => $ipAddress,
                'endpoint' => $endpoint,
                'violation_count' => $violations,
            ],
            "Rate limit violation from IP {$ipAddress}"
        );

        // Auto-block after 5 violations
        if ($violations >= 5) {
            $this->blockIP(
                $ipAddress,
                'Automatic: Multiple rate limit violations',
                1, // 1 hour block
                Auth::id()
            );
        }
    }

    /**
     * Track suspicious activity (failed auth, permission denied, etc.)
     * Auto-block after threshold
     */
    public function trackSuspiciousActivity(string $ipAddress, string $activityType): void
    {
        $cacheKey = "suspicious_activity:{$ipAddress}:{$activityType}";
        $count = (int) Cache::get($cacheKey, 0);
        $count++;

        Cache::put($cacheKey, $count, 3600);

        // Log activity
        $this->auditService->logAction(
            'suspicious_activity_tracked',
            'Attendance',
            null,
            [
                'ip_address' => $ipAddress,
                'activity_type' => $activityType,
                'count' => $count,
            ]
        );

        // Auto-block after 10 failed attempts (login, permission denied, etc.)
        if ($count >= 10) {
            $this->blockIP(
                $ipAddress,
                "Automatic: Multiple {$activityType} attempts",
                2, // 2 hour block
                Auth::id()
            );
        }
    }

    /**
     * Block an IP address
     *
     * @param string $ipAddress
     * @param string $reason
     * @param int $durationHours Duration in hours (null = indefinite)
     * @param int|null $blockedByUserId
     * @param string|null $notes
     * @return IPBlockList
     */
    public function blockIP(
        string $ipAddress,
        string $reason,
        ?int $durationHours = null,
        ?int $blockedByUserId = null,
        ?string $notes = null
    ): IPBlockList {
        $unblockAt = $durationHours ? now()->addHours($durationHours) : null;

        $block = IPBlockList::block(
            $ipAddress,
            $reason,
            $blockedByUserId,
            $unblockAt,
            $notes
        );

        // Log the blocking action
        $this->auditService->logAction(
            'ip_blocked',
            'IPBlockList',
            $block->id,
            [
                'ip_address' => $ipAddress,
                'duration_hours' => $durationHours,
                'reason' => $reason,
            ],
            "IP {$ipAddress} blocked for: {$reason}"
        );

        // Clear violation cache for this IP
        Cache::forget("rate_limit_violations:{$ipAddress}:*");
        Cache::forget("suspicious_activity:{$ipAddress}:*");

        return $block;
    }

    /**
     * Unblock an IP address
     */
    public function unblockIP(string $ipAddress, ?string $reason = null): bool
    {
        $success = IPBlockList::unblock($ipAddress);

        if ($success) {
            $this->auditService->logAction(
                'ip_unblocked',
                'IPBlockList',
                null,
                [
                    'ip_address' => $ipAddress,
                    'reason' => $reason,
                ]
            );
        }

        return $success;
    }

    /**
     * Get active blocks
     */
    public function getActiveBlocks()
    {
        return IPBlockList::active()
            ->orderBy('blocked_at', 'desc')
            ->paginate(25);
    }

    /**
     * Get block info for an IP
     */
    public function getBlockInfo(string $ipAddress): ?IPBlockList
    {
        return IPBlockList::where('ip_address', $ipAddress)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('unblock_at')
                    ->orWhere('unblock_at', '>', now());
            })
            ->first();
    }

    /**
     * Get violation history for an IP (last 24 hours)
     */
    public function getViolationHistory(string $ipAddress): array
    {
        return [
            'rate_limit_violations' => (int) Cache::get("rate_limit_violations:{$ipAddress}:*", 0),
            'suspicious_activities' => (int) Cache::get("suspicious_activity:{$ipAddress}:*", 0),
            'is_blocked' => IPBlockList::isBlocked($ipAddress),
            'block_info' => $this->getBlockInfo($ipAddress),
        ];
    }

    /**
     * Auto-unblock IPs with expired blocks
     */
    public function cleanupExpiredBlocks(): int
    {
        $unblocked = IPBlockList::where('unblock_at', '<=', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);

        if ($unblocked > 0) {
            $this->auditService->logAction(
                'cleanup_expired_blocks',
                'IPBlockList',
                null,
                ['count' => $unblocked],
                "Automatically unblocked {$unblocked} IP addresses"
            );
        }

        return $unblocked;
    }
}
