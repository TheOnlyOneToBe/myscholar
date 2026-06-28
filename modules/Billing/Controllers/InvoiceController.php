<?php

namespace Modules\Billing\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Services\BillingService;
use Modules\Auth\Models\User;

class InvoiceController
{
    public function __construct(
        private BillingService $billingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Invoice::class);

        $query = Invoice::query();

        if ($request->user()->hasRole('enseignant')) {
            $query->whereHas('student.class', fn($q) =>
                $q->where('class_id', $request->user()->class_id)
            );
        } elseif ($request->user()->hasRole('student')) {
            $student = $request->user()->student;
            $query->where('student_id', $student->id ?? null);
        }

        $invoices = $query
            ->with('student', 'student.class')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $invoices->items(),
            'pagination' => [
                'total' => $invoices->total(),
                'per_page' => $invoices->perPage(),
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
            ]
        ]);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $this->authorize('view', $invoice);

        return response()->json([
            'data' => $invoice->load('student', 'student.class', 'payments', 'scholarships')
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Invoice::class);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
            'description' => 'nullable|string|max:500',
        ]);

        $invoice = $this->billingService->createInvoice(
            $validated['student_id'],
            $validated['fee_structure_id'],
            $validated['amount'],
            new \DateTime($validated['due_date']),
            $validated['description'] ?? null
        );

        return response()->json([
            'data' => $invoice,
            'message' => 'Invoice created successfully'
        ], 201);
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'due_date' => 'sometimes|date',
            'description' => 'nullable|string|max:500',
            'status' => 'sometimes|in:draft,issued,overdue,paid,cancelled',
        ]);

        $invoice->update($validated);

        return response()->json([
            'data' => $invoice,
            'message' => 'Invoice updated successfully'
        ]);
    }

    public function delete(Invoice $invoice): JsonResponse
    {
        $this->authorize('delete', $invoice);

        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }

    public function markAsOverdue(Invoice $invoice): JsonResponse
    {
        $this->authorize('markAsOverdue', Invoice::class);

        $invoice->update(['status' => 'overdue']);

        return response()->json([
            'data' => $invoice,
            'message' => 'Invoice marked as overdue'
        ]);
    }

    public function export(Request $request): JsonResponse
    {
        $this->authorize('export', Invoice::class);

        $query = Invoice::query();

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->get('date_to'));
        }

        $invoices = $query->get();

        return response()->json([
            'data' => $invoices,
            'total' => $invoices->count(),
            'exported_at' => now()
        ]);
    }
}
