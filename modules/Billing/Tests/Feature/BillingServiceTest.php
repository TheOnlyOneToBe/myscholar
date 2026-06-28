<?php

namespace Modules\Billing\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Billing\Services\BillingService;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Modules\Billing\Models\Scholarship;
use Modules\Students\Models\Student;

class BillingServiceTest extends TestCase
{
    protected BillingService $billingService;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->billingService = app(BillingService::class);

        $this->admin = User::factory()->create();
        $this->admin->giveRole('admin');
    }

    public function test_get_invoice_stats_returns_array()
    {
        $stats = $this->billingService->getInvoiceStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_invoices', $stats);
        $this->assertArrayHasKey('total_pending', $stats);
        $this->assertArrayHasKey('total_overdue', $stats);
        $this->assertArrayHasKey('total_paid', $stats);
        $this->assertArrayHasKey('total_amount_due', $stats);
        $this->assertArrayHasKey('total_collected', $stats);
    }


    public function test_generate_invoice_number_format()
    {
        $this->actingAs($this->admin);

        $number = $this->billingService->generateInvoiceNumber();

        $this->assertStringContainsString('INV-', $number);
        $this->assertMatchesRegularExpression('/INV-\d{4}-\d{5}/', $number);
    }

    public function test_get_payment_methods()
    {
        $methods = $this->billingService->getPaymentMethods();

        $this->assertIsArray($methods);
        $this->assertContains('cash', $methods);
        $this->assertContains('bank_transfer', $methods);
    }

    public function test_get_recent_payments_returns_array()
    {
        $payments = $this->billingService->getRecentPayments(10);

        $this->assertIsArray($payments);
    }

    public function test_get_scholarship_stats()
    {
        $stats = $this->billingService->getScholarshipStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_scholarships', $stats);
        $this->assertArrayHasKey('pending_approval', $stats);
        $this->assertArrayHasKey('approved_scholarships', $stats);
        $this->assertArrayHasKey('total_scholarship_amount', $stats);
    }

    public function test_get_overdue_invoices_returns_array()
    {
        $invoices = $this->billingService->getOverdueInvoices();

        $this->assertIsArray($invoices);
    }

    public function test_generate_payment_report()
    {
        $startDate = now()->subDays(7)->toDateString();
        $endDate = now()->toDateString();

        $report = $this->billingService->generatePaymentReport($startDate, $endDate);

        $this->assertIsArray($report);
        $this->assertArrayHasKey('period', $report);
        $this->assertArrayHasKey('total_payments', $report);
        $this->assertArrayHasKey('total_amount', $report);
        $this->assertArrayHasKey('by_method', $report);
    }
}
