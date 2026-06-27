<?php

namespace Modules\Config\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Config\Models\SchoolYear;

class SchoolYearController extends Controller
{
    public function index(): JsonResponse
    {
        $schoolYears = SchoolYear::orderBy('start_year', 'desc')->get();

        return response()->json([
            'data' => $schoolYears,
        ]);
    }

    public function show(SchoolYear $schoolYear): JsonResponse
    {
        return response()->json([
            'data' => $schoolYear,
        ]);
    }

    public function current(): JsonResponse
    {
        $current = SchoolYear::where('is_active', true)->first();

        if (!$current) {
            return response()->json([
                'message' => 'Aucune année scolaire active configurée',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'data' => $current,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_years'],
            'start_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'end_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $schoolYear = SchoolYear::create($validated);

        return response()->json([
            'message' => 'Année scolaire créée avec succès.',
            'data' => $schoolYear,
        ], 201);
    }

    public function update(Request $request, SchoolYear $schoolYear): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'end_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $schoolYear->update($validated);

        return response()->json([
            'message' => 'Année scolaire mise à jour avec succès.',
            'data' => $schoolYear->fresh(),
        ]);
    }

    public function destroy(SchoolYear $schoolYear): JsonResponse
    {
        if ($schoolYear->is_active) {
            return response()->json([
                'message' => 'Impossible de supprimer l\'année scolaire active.',
            ], 422);
        }

        $schoolYear->delete();

        return response()->json([
            'message' => 'Année scolaire supprimée avec succès.',
        ]);
    }

    public function activate(SchoolYear $schoolYear): JsonResponse
    {
        SchoolYear::where('is_active', true)->update(['is_active' => false]);
        $schoolYear->update(['is_active' => true]);

        return response()->json([
            'message' => 'Année scolaire activée avec succès.',
            'data' => $schoolYear->fresh(),
        ]);
    }

    public function list(): JsonResponse
    {
        $schoolYears = SchoolYear::orderBy('start_year', 'desc')
            ->get()
            ->map(fn ($year) => [
                'id' => $year->id,
                'name' => $year->name,
                'start_year' => $year->start_year,
                'end_year' => $year->end_year,
                'is_active' => $year->is_active,
                'start_date' => $year->start_date,
                'end_date' => $year->end_date,
            ]);

        return response()->json([
            'data' => $schoolYears,
        ]);
    }
}
