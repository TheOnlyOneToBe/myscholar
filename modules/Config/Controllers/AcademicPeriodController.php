<?php

namespace Modules\Config\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Config\Models\AcademicPeriod;
use Modules\Config\Services\AcademicTermService;
use Illuminate\Routing\Controller;

class AcademicPeriodController extends Controller
{
    public function __construct(
        protected AcademicTermService $academicTermService
    ) {}

    /**
     * Récupère tous les trimestres d'une année
     */
    public function index(int $year = null): JsonResponse
    {
        $year = $year ?? now()->year;
        $periods = $this->academicTermService->getTermsForYear($year);

        return response()->json([
            'data' => $periods,
            'year' => $year,
        ]);
    }

    /**
     * Récupère un trimestre spécifique
     */
    public function show(int $termNumber, int $year = null): JsonResponse
    {
        $year = $year ?? now()->year;
        $period = $this->academicTermService->getTermByNumber($termNumber, $year);

        if (!$period) {
            return response()->json([
                'error' => 'Trimestre non trouvé',
            ], 404);
        }

        return response()->json([
            'data' => $period,
        ]);
    }

    /**
     * Met à jour un trimestre
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $period = AcademicPeriod::find($id);
        if (!$period) {
            return response()->json([
                'error' => 'Trimestre non trouvé',
            ], 404);
        }

        $period->update($validated);
        $this->academicTermService->clearCache($period->academic_year);

        return response()->json([
            'message' => 'Trimestre mis à jour avec succès',
            'data' => $period,
        ]);
    }

    /**
     * Récupère le trimestre actuel
     */
    public function current(): JsonResponse
    {
        $current = $this->academicTermService->getCurrentTerm();

        if (!$current) {
            return response()->json([
                'error' => 'Aucun trimestre actif trouvé',
            ], 404);
        }

        return response()->json([
            'data' => $current,
        ]);
    }

    /**
     * Initialise les trimestres par défaut pour une année
     */
    public function initialize(Request $request): JsonResponse
    {
        $year = $request->input('year', now()->year);

        try {
            $this->academicTermService->initializeDefaultTerms($year);

            return response()->json([
                'message' => "Trimestres initialisés pour l'année $year",
                'data' => $this->academicTermService->getTermsForYear($year),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => "Erreur lors de l'initialisation: " . $e->getMessage(),
            ], 500);
        }
    }
}
