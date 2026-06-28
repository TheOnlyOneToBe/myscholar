<?php

namespace Modules\Billing\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Billing\Models\Payment;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Services\BillingService;

class PaymentController
{
    public function __construct(
        private BillingService $billingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::query();

        if ($request->user()->hasRole('student')) {
            $student = $request->user()->student;
            $query->whereHas('invoice', fn($q) =>
                $q->where('student_id', $student->id ?? null)
            );
        }

        $payments = $query
            ->with('invoice', 'invoice.student')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $payments->items(),
            'pagination' => [
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
            ]
        ]);
    }

    public function show(Payment $payment): JsonResponse
    {
        $this->authorize('view', $payment);

        return response()->json([
            'data' => $payment->load('invoice', 'invoice.student')
        ]);
    }

    public function record(Request $request): JsonResponse
    {
        $this->authorize('record', Payment::class);

        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check,mobile_money',
            'reference_number' => 'nullable|string|max:100|unique:payments',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment = $this->billingService->recordPayment(
            $validated['invoice_id'],
            $validated['amount'],
            $validated['payment_method'],
            $validated['reference_number'] ?? null,
            $validated['notes'] ?? null,
            $request->user()->id
        );

        return response()->json([
            'data' => $payment,
            'message' => 'Payment recorded successfully'
        ], 201);
    }

    public function refund(Request $request, Payment $payment): JsonResponse
    {
        $this->authorize('refund', Payment::class);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'required|string|max:500',
        ]);

        $refundPayment = Payment::create([
            'invoice_id' => $payment->invoice_id,
            'amount' => -abs($validated['amount']),
            'payment_method' => $payment->payment_method,
            'reference_number' => 'REFUND-' . $payment->reference_number,
            'notes' => 'Refund reason: ' . $validated['reason'],
            'paid_at' => now(),
            'recorded_by' => $request->user()->id,
        ]);

        return response()->json([
            'data' => $refundPayment,
            'message' => 'Refund processed successfully'
        ]);
    }

    public function delete(Payment $payment): JsonResponse
    {
        $this->authorize('delete', $payment);

        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }

    public function export(Request $request): JsonResponse
    {
        $this->authorize('export', Payment::class);

        $query = Payment::query();

        if ($request->has('method')) {
            $query->where('payment_method', $request->get('method'));
        }

        if ($request->has('date_from')) {
            $query->where('paid_at', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('paid_at', '<=', $request->get('date_to'));
        }

        $payments = $query->with('invoice')->get();

        return response()->json([
            'data' => $payments,
            'total' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'exported_at' => now()
        ]);
    }
}
