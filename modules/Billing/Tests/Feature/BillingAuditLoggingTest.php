<?php

namespace Modules\Billing\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Billing\Services\BillingAuditService;
use Modules\Audit\Models\AuditLog;

class BillingAuditLoggingTest extends TestCase
{
    protected BillingAuditService $auditService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auditService = app(BillingAuditService::class);
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_audit_service_is_registered()
    {
        $this->assertNotNull($this->auditService);
        $this->assertInstanceOf(BillingAuditService::class, $this->auditService);
    }

    public function test_log_bulk_invoices_generated()
    {
        $this->auditService->logBulkInvoicesGenerated(10, ['class_id' => 1]);

        $log = AuditLog::where('action', 'bulk_invoices_generated')->first();

        $this->assertNotNull($log);
    }

    public function test_log_payment_report_exported()
    {
        $this->auditService->logPaymentReportExported('2024-01', ['status' => 'paid']);

        $log = AuditLog::where('action', 'payment_report_exported')->first();

        $this->assertNotNull($log);
    }

    public function test_log_permission_denied()
    {
        $this->auditService->logPermissionDenied('refund_payment', 'Insufficient permissions');

        $log = AuditLog::where('action', 'billing_permission_denied')->first();

        $this->assertNotNull($log);
        $this->assertEquals('warning', $log->severity);
    }

    public function test_audit_logs_include_ip_address()
    {
        $this->auditService->logInvoiceCreated(1, ['amount' => 1000]);

        $log = AuditLog::where('action', 'invoice_created')->first();

        $this->assertNotNull($log->ip_address);
    }

    public function test_audit_logs_include_user_agent()
    {
        $this->auditService->logInvoiceCreated(1, ['amount' => 1000]);

        $log = AuditLog::where('action', 'invoice_created')->first();

        $this->assertNotNull($log->user_agent);
    }
}
