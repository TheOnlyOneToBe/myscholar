<?php

namespace Modules\Billing\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Billing\Models\Scholarship;
use Modules\Billing\Services\BillingService;

class ScholarshipController
{
    public function __construct(
        private BillingService $billingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Scholarship::class);

        $query = Scholarship::query();

        if ($request->user()->hasRole('student')) {
            $student = $request->user()->student;
            $query->where('student_id', $student->id ?? null);
        }

        $scholarships = $query
            ->with('student')
            ->when($request->has('status'), fn($q) =>
                $q->where('status', $request->get('status'))
            )
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $scholarships->items(),
            'pagination' => [
                'total' => $scholarships->total(),
                'per_page' => $scholarships->perPage(),
                'current_page' => $scholarships->currentPage(),
                'last_page' => $scholarships->lastPage(),
            ]
        ]);
    }

    public function show(Scholarship $scholarship): JsonResponse
    {
        $this->authorize('view', $scholarship);

        return response()->json([
            'data' => $scholarship->load('student', 'invoices')
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Scholarship::class);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:full,partial,merit,need-based',
            'percentage' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        $scholarship = Scholarship::create([
            'student_id' => $validated['student_id'],
            'type' => $validated['type'],
            'percentage' => $validated['percentage'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'data' => $scholarship,
            'message' => 'Scholarship application created successfully'
        ], 201);
    }

    public function update(Request $request, Scholarship $scholarship): JsonResponse
    {
        $this->authorize('update', $scholarship);

        $validated = $request->validate([
            'percentage' => 'sometimes|numeric|min:0|max:100',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'reason' => 'sometimes|nullable|string|max:500',
        ]);

        $scholarship->update($validated);

        return response()->json([
            'data' => $scholarship,
            'message' => 'Scholarship updated successfully'
        ]);
    }

    public function approve(Scholarship $scholarship): JsonResponse
    {
        $this->authorize('approve', Scholarship::class);

        $scholarship->update(['status' => 'approved']);

        return response()->json([
            'data' => $scholarship,
            'message' => 'Scholarship approved successfully'
        ]);
    }

    public function reject(Request $request, Scholarship $scholarship): JsonResponse
    {
        $this->authorize('reject', Scholarship::class);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $scholarship->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return response()->json([
            'data' => $scholarship,
            'message' => 'Scholarship rejected'
        ]);
    }

    public function delete(Scholarship $scholarship): JsonResponse
    {
        $this->authorize('delete', $scholarship);

        $scholarship->delete();

        return response()->json([
            'message' => 'Scholarship deleted successfully'
        ]);
    }
}
