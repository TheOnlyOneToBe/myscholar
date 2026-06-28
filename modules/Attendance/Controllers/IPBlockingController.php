<?php

namespace Modules\Attendance\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Attendance\Models\IPBlockList;
use Modules\Attendance\Services\IPBlockingService;
use Modules\Audit\Services\AuditService;

class IPBlockingController extends Controller
{
    protected IPBlockingService $ipBlockingService;
    protected AuditService $auditService;

    public function __construct(IPBlockingService $ipBlockingService, AuditService $auditService)
    {
        $this->ipBlockingService = $ipBlockingService;
        $this->auditService = $auditService;
        $this->middleware('auth:api');
        $this->middleware('can:attendance.manage_ip_blocking');
    }

    public function getActiveBlocks()
    {
        $blocks = $this->ipBlockingService->getActiveBlocks();

        $this->auditService->logAction(
            'ip_blocks_viewed',
            'IPBlockList',
            null,
            ['count' => $blocks->total()],
            'Viewed active IP blocks'
        );

        return response()->json([
            'data' => $blocks->items(),
            'pagination' => [
                'total' => $blocks->total(),
                'per_page' => $blocks->perPage(),
                'current_page' => $blocks->currentPage(),
                'last_page' => $blocks->lastPage(),
            ],
        ]);
    }

    public function blockIP(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'required|string|max:255',
            'duration_hours' => 'nullable|integer|min:1|max:8760',
            'notes' => 'nullable|string|max:500',
        ]);

        $block = $this->ipBlockingService->blockIP(
            $validated['ip_address'],
            $validated['reason'],
            $validated['duration_hours'] ?? null,
            auth()->id(),
            $validated['notes'] ?? null
        );

        return response()->json([
            'message' => 'IP blocked successfully',
            'data' => $block,
        ], 201);
    }

    public function unblockIP(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:255',
        ]);

        $success = $this->ipBlockingService->unblockIP(
            $validated['ip_address'],
            $validated['reason']
        );

        if (!$success) {
            return response()->json([
                'message' => 'IP address not found in blocklist',
            ], 404);
        }

        return response()->json([
            'message' => 'IP unblocked successfully',
        ]);
    }

    public function getBlockInfo($ipAddress)
    {
        $blockInfo = $this->ipBlockingService->getBlockInfo($ipAddress);

        if (!$blockInfo) {
            return response()->json([
                'message' => 'IP address is not blocked',
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => $blockInfo,
        ]);
    }

    public function getViolationHistory($ipAddress)
    {
        $history = $this->ipBlockingService->getViolationHistory($ipAddress);

        return response()->json([
            'data' => $history,
        ]);
    }

    public function cleanupExpiredBlocks()
    {
        $unblocked = $this->ipBlockingService->cleanupExpiredBlocks();

        return response()->json([
            'message' => "Cleaned up {$unblocked} expired IP blocks",
            'unblocked_count' => $unblocked,
        ]);
    }
}
