<?php

namespace Modules\Billing\Services;

use Modules\Audit\Models\AuditLog;
use Modules\Auth\Models\User;

class BillingAuditService
{
    public function logInvoiceCreated(int $invoiceId, array $data): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'invoice_created',
            'model' => 'Invoice',
            'model_id' => $invoiceId,
            'changes' => json_encode($data),
            'severity' => 'info',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logInvoiceUpdated(int $invoiceId, array $changes): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'invoice_updated',
            'model' => 'Invoice',
            'model_id' => $invoiceId,
            'changes' => json_encode($changes),
            'severity' => 'info',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logPaymentRecorded(int $paymentId, array $data): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'payment_recorded',
            'model' => 'Payment',
            'model_id' => $paymentId,
            'changes' => json_encode($data),
            'severity' => 'warning',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logScholarshipApproved(int $scholarshipId, array $data): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'scholarship_approved',
            'model' => 'Scholarship',
            'model_id' => $scholarshipId,
            'changes' => json_encode($data),
            'severity' => 'warning',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logRefundProcessed(int $paymentId, array $data): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'refund_processed',
            'model' => 'Payment',
            'model_id' => $paymentId,
            'changes' => json_encode($data),
            'severity' => 'critical',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logFeeStructureCreated(int $feeStructureId, array $data): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'fee_structure_created',
            'model' => 'FeeStructure',
            'model_id' => $feeStructureId,
            'changes' => json_encode($data),
            'severity' => 'info',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logBulkInvoicesGenerated(int $count, array $data): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'bulk_invoices_generated',
            'model' => 'Invoice',
            'changes' => json_encode([
                'count' => $count,
                'data' => $data,
            ]),
            'severity' => 'info',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logPaymentReportExported(string $period, array $filters): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'payment_report_exported',
            'model' => 'Payment',
            'changes' => json_encode([
                'period' => $period,
                'filters' => $filters,
            ]),
            'severity' => 'info',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logPermissionDenied(string $action, string $reason): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'billing_permission_denied',
            'model' => 'Billing',
            'changes' => json_encode([
                'attempted_action' => $action,
                'reason' => $reason,
            ]),
            'severity' => 'warning',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
