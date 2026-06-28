<?php

namespace Modules\Billing\Services;

use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Modules\Billing\Models\Scholarship;
use Modules\Billing\Models\FeeStructure;
use Modules\Billing\Models\PaymentPlan;
use Modules\Students\Models\Student;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function getInvoiceStats(): array
    {
        return [
            'total_invoices' => Invoice::count(),
            'total_pending' => Invoice::whereIn('status', ['draft', 'issued', 'partial'])->count(),
            'total_overdue' => Invoice::where('status', 'overdue')->count(),
            'total_paid' => Invoice::where('status', 'paid')->count(),
            'total_amount_due' => Invoice::whereIn('status', ['draft', 'issued', 'partial', 'overdue'])
                ->sum(DB::raw('amount - amount_paid')),
            'total_collected' => Payment::sum('amount'),
        ];
    }

    public function getStudentOutstandingBalance(Student $student): float
    {
        return $student->invoices()
            ->whereIn('status', ['draft', 'issued', 'partial', 'overdue'])
            ->sum(DB::raw('amount - amount_paid'));
    }

    public function getStudentInvoices(Student $student): array
    {
        return $student->invoices()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => $invoice->amount,
                    'amount_paid' => $invoice->amount_paid,
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date,
                    'is_overdue' => $invoice->isOverdue(),
                ];
            })
            ->toArray();
    }

    public function createInvoice(array $data): Invoice
    {
        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'student_id' => $data['student_id'],
            'fee_structure_id' => $data['fee_structure_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'FCFA',
            'issue_date' => $data['issue_date'] ?? now(),
            'due_date' => $data['due_date'],
            'status' => 'issued',
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function recordPayment(Invoice $invoice, array $data): Payment
    {
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'FCFA',
            'payment_method' => $data['payment_method'],
            'payment_date' => $data['payment_date'] ?? now(),
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null,
            'processed_by_user_id' => auth()->id(),
        ]);

        $payment->recordPayment();

        return $payment;
    }

    public function approveScholarship(Scholarship $scholarship): void
    {
        $scholarship->update(['status' => 'approved']);

        if ($scholarship->invoice) {
            $remainingAmount = $scholarship->invoice->getRemainingAmount();
            if ($remainingAmount <= $scholarship->amount) {
                $scholarship->invoice->update(['status' => 'paid']);
            }
        }
    }

    public function getScholarshipStats(): array
    {
        return [
            'total_scholarships' => Scholarship::count(),
            'pending_approval' => Scholarship::where('status', 'pending')->count(),
            'approved_scholarships' => Scholarship::where('status', 'approved')->count(),
            'total_scholarship_amount' => Scholarship::sum('amount'),
        ];
    }

    public function getPaymentMethods(): array
    {
        return Payment::getPaymentMethods();
    }

    public function getRecentPayments(int $limit = 10): array
    {
        return Payment::with('invoice.student')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                    'student_name' => $payment->invoice->student->full_name ?? 'N/A',
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'payment_date' => $payment->payment_date,
                    'reference' => $payment->reference_number,
                ];
            })
            ->toArray();
    }

    public function generatePaymentReport(string $startDate, string $endDate): array
    {
        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->with('invoice.student', 'processedBy')
            ->get();

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'by_method' => $payments->groupBy('payment_method')->map(fn($group) => [
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ])->toArray(),
            'by_processor' => $payments->groupBy('processed_by_user_id')->map(fn($group) => [
                'processor' => $group->first()->processedBy->name ?? 'Unknown',
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ])->toArray(),
        ];
    }

    public function getOverdueInvoices(): array
    {
        return Invoice::where('status', 'overdue')
            ->with('student')
            ->orderBy('due_date')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'student_name' => $invoice->student->full_name ?? 'N/A',
                    'amount_due' => $invoice->getRemainingAmount(),
                    'due_date' => $invoice->due_date,
                    'days_overdue' => now()->diffInDays($invoice->due_date),
                ];
            })
            ->toArray();
    }

    public function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $latestInvoice = Invoice::where('invoice_number', 'like', "INV-{$year}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $latestInvoice ? intval(substr($latestInvoice->invoice_number, -5)) + 1 : 1;

        return sprintf('INV-%d-%05d', $year, $nextNumber);
    }
}
