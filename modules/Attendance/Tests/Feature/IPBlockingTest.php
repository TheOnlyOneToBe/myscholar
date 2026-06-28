<?php

namespace Modules\Attendance\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Attendance\Models\IPBlockList;
use Modules\Attendance\Services\IPBlockingService;
use Illuminate\Support\Facades\Cache;

class IPBlockingTest extends TestCase
{
    protected IPBlockingService $ipBlockingService;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ipBlockingService = app(IPBlockingService::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super_administrator');
        $this->admin->givePermissionTo('attendance.manage_ip_blocking');
    }

    public function test_can_block_ip_address()
    {
        $ipAddress = '192.168.1.1';
        $reason = 'Suspicious activity detected';

        $block = $this->ipBlockingService->blockIP($ipAddress, $reason, 1, $this->admin->id);

        $this->assertNotNull($block);
        $this->assertTrue($block->is_active);
        $this->assertEquals($ipAddress, $block->ip_address);
        $this->assertEquals($reason, $block->reason);
    }

    public function test_can_unblock_ip_address()
    {
        $ipAddress = '192.168.1.1';
        $this->ipBlockingService->blockIP($ipAddress, 'Test block', null, $this->admin->id);

        $success = $this->ipBlockingService->unblockIP($ipAddress);

        $this->assertTrue($success);
        $this->assertFalse(IPBlockList::isBlocked($ipAddress));
    }

    public function test_ip_blocking_service_auto_blocks_after_rate_limit_violations()
    {
        $ipAddress = '192.168.1.2';
        Cache::forget("rate_limit_violations:{$ipAddress}:*");

        for ($i = 0; $i < 5; $i++) {
            $this->ipBlockingService->trackRateLimitViolation($ipAddress, 'bulk_operation');
        }

        $this->assertTrue(IPBlockList::isBlocked($ipAddress));
        $block = IPBlockList::where('ip_address', $ipAddress)->first();
        $this->assertStringContainsString('rate limit violations', $block->reason);
    }

    public function test_ip_blocking_service_auto_blocks_after_suspicious_activity()
    {
        $ipAddress = '192.168.1.3';
        Cache::forget("suspicious_activity:{$ipAddress}:*");

        for ($i = 0; $i < 10; $i++) {
            $this->ipBlockingService->trackSuspiciousActivity($ipAddress, 'failed_login');
        }

        $this->assertTrue(IPBlockList::isBlocked($ipAddress));
    }

    public function test_get_active_blocks()
    {
        $this->ipBlockingService->blockIP('192.168.1.4', 'Test', 1, $this->admin->id);
        $this->ipBlockingService->blockIP('192.168.1.5', 'Test', 1, $this->admin->id);

        $blocks = $this->ipBlockingService->getActiveBlocks();

        $this->assertGreaterThanOrEqual(2, $blocks->total());
    }

    public function test_get_block_info()
    {
        $ipAddress = '192.168.1.6';
        $this->ipBlockingService->blockIP($ipAddress, 'Test block', 1, $this->admin->id);

        $blockInfo = $this->ipBlockingService->getBlockInfo($ipAddress);

        $this->assertNotNull($blockInfo);
        $this->assertEquals($ipAddress, $blockInfo->ip_address);
    }

    public function test_get_violation_history()
    {
        $ipAddress = '192.168.1.7';
        $this->ipBlockingService->trackRateLimitViolation($ipAddress, 'bulk_operation');

        $history = $this->ipBlockingService->getViolationHistory($ipAddress);

        $this->assertIsArray($history);
        $this->assertTrue(isset($history['is_blocked']));
        $this->assertTrue(isset($history['block_info']));
    }

    public function test_cleanup_expired_blocks()
    {
        $ipAddress = '192.168.1.8';
        $this->ipBlockingService->blockIP($ipAddress, 'Test', 0, $this->admin->id); // 0 hours = already expired

        $unblocked = $this->ipBlockingService->cleanupExpiredBlocks();

        $this->assertGreaterThanOrEqual(1, $unblocked);
    }

    public function test_api_block_ip_endpoint()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson('/api/attendance/ip-blocking/block', [
            'ip_address' => '192.168.1.9',
            'reason' => 'API test block',
            'duration_hours' => 2,
            'notes' => 'Testing API endpoint',
        ]);

        $response->assertCreated();
        $this->assertTrue(IPBlockList::isBlocked('192.168.1.9'));
    }

    public function test_api_unblock_ip_endpoint()
    {
        $ipAddress = '192.168.1.10';
        $this->ipBlockingService->blockIP($ipAddress, 'Test', 1, $this->admin->id);

        $this->actingAs($this->admin);
        $response = $this->postJson('/api/attendance/ip-blocking/unblock', [
            'ip_address' => $ipAddress,
            'reason' => 'Manual unblock',
        ]);

        $response->assertOk();
        $this->assertFalse(IPBlockList::isBlocked($ipAddress));
    }

    public function test_api_get_active_blocks_endpoint()
    {
        $this->ipBlockingService->blockIP('192.168.1.11', 'Test', 1, $this->admin->id);

        $this->actingAs($this->admin);
        $response = $this->getJson('/api/attendance/ip-blocking/active-blocks');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ip_address',
                    'reason',
                    'is_active',
                ]
            ],
            'pagination' => [
                'total',
                'per_page',
                'current_page',
                'last_page',
            ]
        ]);
    }

    public function test_api_get_block_info_endpoint()
    {
        $ipAddress = '192.168.1.12';
        $this->ipBlockingService->blockIP($ipAddress, 'Test', 1, $this->admin->id);

        $this->actingAs($this->admin);
        $response = $this->getJson("/api/attendance/ip-blocking/info/{$ipAddress}");

        $response->assertOk();
        $response->assertJsonStructure(['data']);
    }

    public function test_api_get_violation_history_endpoint()
    {
        $ipAddress = '192.168.1.13';
        $this->ipBlockingService->trackRateLimitViolation($ipAddress, 'bulk_operation');

        $this->actingAs($this->admin);
        $response = $this->getJson("/api/attendance/ip-blocking/violations/{$ipAddress}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'rate_limit_violations',
                'suspicious_activities',
                'is_blocked',
                'block_info',
            ]
        ]);
    }

    public function test_api_cleanup_expired_blocks_endpoint()
    {
        $this->actingAs($this->admin);
        $response = $this->postJson('/api/attendance/ip-blocking/cleanup');

        $response->assertOk();
        $response->assertJsonStructure(['message', 'unblocked_count']);
    }

    public function test_middleware_blocks_requests_from_blocked_ip()
    {
        $ipAddress = '192.168.1.14';
        $this->ipBlockingService->blockIP($ipAddress, 'Test', 1, $this->admin->id);

        $response = $this->withHeaders([
            'X-Forwarded-For' => $ipAddress,
        ])->postJson('/api/attendance/sessions', [
            'date' => now(),
            'class_id' => 1,
            'subject_id' => 1,
        ]);

        // Should be blocked by CheckIPBlocklist middleware
        $response->assertForbidden();
    }

    public function test_rate_limiting_tracks_violations()
    {
        Cache::flush();
        $ipAddress = '192.168.1.15';

        $user = User::factory()->create();
        $user->assignRole('super_administrator');

        // Simulate multiple rate limit violations
        for ($i = 0; $i < 3; $i++) {
            $this->ipBlockingService->trackRateLimitViolation($ipAddress, 'bulk_operation');
        }

        // Should have tracked violations but not blocked yet (needs 5)
        $history = $this->ipBlockingService->getViolationHistory($ipAddress);
        $this->assertFalse($history['is_blocked']);
    }
}
