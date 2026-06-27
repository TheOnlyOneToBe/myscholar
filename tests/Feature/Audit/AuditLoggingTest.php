<?php

namespace Tests\Feature\Audit;

use Modules\Auth\Models\User;
use Modules\Audit\Models\AuditLog;
use Modules\Audit\Services\AuditService;
use Tests\TestCase;

class AuditLoggingTest extends TestCase
{
    protected AuditService $auditService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auditService = app(AuditService::class);
    }

    public function test_can_log_action()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = $this->auditService->logAction(
            'create',
            'User',
            $user->id,
            ['old_values' => [], 'new_values' => ['name' => 'John']],
            'User created'
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create',
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_can_log_http_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = $this->auditService->logRequest(
            'POST',
            'http://localhost/api/users',
            201,
            45.5,
            ['route' => 'users.store']
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'http_request',
            'method' => 'POST',
            'http_status' => 201,
            'severity' => 'info',
        ]);
    }

    public function test_log_marks_4xx_as_error()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = $this->auditService->logRequest(
            'GET',
            'http://localhost/api/users/999',
            404,
            12.3
        );

        $this->assertDatabaseHas('audit_logs', [
            'http_status' => 404,
            'severity' => 'error',
        ]);
    }

    public function test_log_marks_5xx_as_critical()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = $this->auditService->logRequest(
            'POST',
            'http://localhost/api/users',
            500,
            10.0
        );

        $this->assertDatabaseHas('audit_logs', [
            'http_status' => 500,
            'severity' => 'critical',
        ]);
    }

    public function test_can_log_error()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        try {
            throw new \Exception('Test error');
        } catch (\Exception $e) {
            $log = $this->auditService->logError($e, 'test_error');
        }

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'test_error',
            'severity' => 'error',
        ]);
    }

    public function test_can_log_crash()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = $this->auditService->logCrash(
            'Fatal error occurred',
            'Stack trace here'
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'crash',
            'severity' => 'critical',
            'error_message' => 'Fatal error occurred',
        ]);
    }

    public function test_can_log_authentication()
    {
        $user = User::factory()->create();

        $log = $this->auditService->logAuth(
            'login',
            $user,
            '127.0.0.1',
            'Valid credentials'
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'login',
            'entity_type' => 'Auth',
            'user_id' => $user->id,
        ]);
    }

    public function test_can_query_logs_by_action()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->auditService->logAction('create', 'User', $user->id);
        $this->auditService->logAction('update', 'User', $user->id);

        $logs = AuditLog::byAction('create')->get();
        $this->assertCount(1, $logs);
    }

    public function test_can_query_logs_by_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);
        $this->auditService->logAction('create', 'User', $user1->id);

        $this->actingAs($user2);
        $this->auditService->logAction('create', 'User', $user2->id);

        $logs = AuditLog::byUser($user1->id)->get();
        $this->assertCount(1, $logs);
        $this->assertEquals($user1->id, $logs[0]->user_id);
    }

    public function test_can_get_recent_errors()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->auditService->logRequest('GET', 'http://localhost/api/test', 500, 5.0);
        $this->auditService->logRequest('POST', 'http://localhost/api/test', 200, 10.0);

        $errors = $this->auditService->getRecentErrors(24, 10);
        $this->assertGreaterThan(0, $errors->count());
    }

    public function test_can_get_error_statistics()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->auditService->logRequest('GET', 'http://localhost/api/test', 500);
        $this->auditService->logRequest('POST', 'http://localhost/api/test', 404);
        $this->auditService->logAction('create', 'User', $user->id);

        $stats = $this->auditService->getErrorStats(30);
        $this->assertGreaterThan(0, $stats['total_errors']);
    }

    public function test_audit_log_has_relations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = $this->auditService->logAction('create', 'User', $user->id);

        $this->assertNotNull($log->user);
        $this->assertEquals($user->id, $log->user->id);
    }

    public function test_can_filter_by_severity()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->auditService->logRequest('GET', 'http://localhost/api/test', 500); // critical
        $this->auditService->logRequest('GET', 'http://localhost/api/test', 200); // info

        $criticals = AuditLog::bySeverity('critical')->get();
        $this->assertGreaterThan(0, $criticals->count());
    }
}
