<?php

namespace Modules\Billing\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Billing\Models\FeeStructure;

class FeeStructureController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', FeeStructure::class);

        $query = FeeStructure::query();

        $feeStructures = $query
            ->when($request->has('class_id'), fn($q) =>
                $q->where('class_id', $request->get('class_id'))
            )
            ->when($request->get('active_only'), fn($q) =>
                $q->where('is_active', true)
            )
            ->with('class', 'academicYear')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $feeStructures->items(),
            'pagination' => [
                'total' => $feeStructures->total(),
                'per_page' => $feeStructures->perPage(),
                'current_page' => $feeStructures->currentPage(),
                'last_page' => $feeStructures->lastPage(),
            ]
        ]);
    }

    public function show(FeeStructure $feeStructure): JsonResponse
    {
        $this->authorize('view', $feeStructure);

        return response()->json([
            'data' => $feeStructure->load('class', 'academicYear', 'items')
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', FeeStructure::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:school_years,id',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'items' => 'nullable|array',
            'items.*.name' => 'required|string|max:255',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        $feeStructure = FeeStructure::create([
            'name' => $validated['name'],
            'class_id' => $validated['class_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'total_amount' => $validated['total_amount'],
            'currency' => $validated['currency'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($validated['items'] ?? null) {
            foreach ($validated['items'] as $item) {
                $feeStructure->items()->create($item);
            }
        }

        return response()->json([
            'data' => $feeStructure->load('items'),
            'message' => 'Fee structure created successfully'
        ], 201);
    }

    public function update(Request $request, FeeStructure $feeStructure): JsonResponse
    {
        $this->authorize('update', $feeStructure);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'total_amount' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:10',
            'description' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $feeStructure->update($validated);

        return response()->json([
            'data' => $feeStructure->load('items'),
            'message' => 'Fee structure updated successfully'
        ]);
    }

    public function delete(FeeStructure $feeStructure): JsonResponse
    {
        $this->authorize('delete', $feeStructure);

        $feeStructure->delete();

        return response()->json([
            'message' => 'Fee structure deleted successfully'
        ]);
    }

    public function activate(FeeStructure $feeStructure): JsonResponse
    {
        $this->authorize('update', $feeStructure);

        $feeStructure->update(['is_active' => true]);

        return response()->json([
            'data' => $feeStructure,
            'message' => 'Fee structure activated'
        ]);
    }

    public function deactivate(FeeStructure $feeStructure): JsonResponse
    {
        $this->authorize('update', $feeStructure);

        $feeStructure->update(['is_active' => false]);

        return response()->json([
            'data' => $feeStructure,
            'message' => 'Fee structure deactivated'
        ]);
    }
}
