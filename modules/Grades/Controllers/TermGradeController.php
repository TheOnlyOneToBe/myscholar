<?php

namespace Modules\Grades\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Grades\Services\TermGradeService;
use Modules\Students\Models\Student;

class TermGradeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TermGradeService $termGradeService
    ) {}

    /**
     * Récupérer les notes d'un élève pour un trimestre
     */
    public function getStudentGradesByTerm(Student $student, Request $request): JsonResponse
    {
        $this->authorize('view', $student);

        try {
            $academicPeriodId = $request->input('academic_period_id');
            $grades = $this->termGradeService->getStudentGradesByTerm($student->id, $academicPeriodId);

            return response()->json([
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'grades' => $grades,
                'total' => count($grades),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des notes',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Récupérer le résumé des notes par trimestre
     */
    public function getTermSummary(Student $student, Request $request): JsonResponse
    {
        $this->authorize('view', $student);

        try {
            $academicPeriodId = $request->input('academic_period_id');
            $summary = $this->termGradeService->getTermSummary($student->id, $academicPeriodId);

            return response()->json($summary);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération du résumé',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Récupérer la moyenne d'un élève pour un trimestre
     */
    public function getTermAverage(Student $student, Request $request): JsonResponse
    {
        $this->authorize('view', $student);

        try {
            $academicPeriodId = $request->input('academic_period_id');
            $average = $this->termGradeService->getStudentTermAverage($student->id, $academicPeriodId);

            return response()->json([
                'student_id' => $student->id,
                'average' => $average,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du calcul de la moyenne',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Récupérer les trimestres disponibles
     */
    public function getAvailableTerms(Request $request): JsonResponse
    {
        try {
            $academicYear = $request->input('academic_year', now()->year);
            $terms = $this->termGradeService->getAvailableTerms($academicYear);

            return response()->json([
                'academic_year' => $academicYear,
                'terms' => $terms,
                'total' => count($terms),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des trimestres',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Récupérer le trimestre actuel
     */
    public function getCurrentTerm(): JsonResponse
    {
        try {
            $term = $this->termGradeService->getCurrentTerm();

            if (!$term) {
                return response()->json([
                    'error' => 'Aucun trimestre actif',
                ], 404);
            }

            return response()->json($term);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération du trimestre actuel',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Récupérer le classement de la classe pour un trimestre
     */
    public function getClassRanking(int $classId, Request $request): JsonResponse
    {
        try {
            $academicPeriodId = $request->input('academic_period_id');
            $ranking = $this->termGradeService->getClassRankingByTerm($classId, $academicPeriodId);

            return response()->json([
                'class_id' => $classId,
                'ranking' => $ranking,
                'total_students' => count($ranking),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération du classement',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Exporter les grades d'une classe par trimestre en CSV
     */
    public function exportClassGradesByTerm(int $classId, Request $request): JsonResponse
    {
        try {
            $academicPeriodId = $request->input('academic_period_id');
            $grades = $this->termGradeService->getClassGradesByTerm($classId, $academicPeriodId);

            $csv = "Élève,Moyenne,Nombre de notes\n";
            foreach ($grades as $studentGrade) {
                $csv .= sprintf(
                    '"%s",%s,%d' . "\n",
                    $studentGrade['student_name'],
                    $studentGrade['average'],
                    $studentGrade['grade_count']
                );
            }

            return response()->json([
                'csv' => base64_encode($csv),
                'filename' => 'grades_class_' . $classId . '_' . now()->format('Y-m-d_H-i-s') . '.csv',
                'count' => count($grades),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'export',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
