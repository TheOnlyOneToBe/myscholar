<?php

namespace Modules\Dashboard\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Dashboard\Services\TermDocumentService;
use Modules\Students\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TermDocumentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private TermDocumentService $documentService
    ) {}

    /**
     * Obtenir les données du bulletin par trimestre
     */
    public function getTermBulletinData(int $studentId, int $academicPeriodId): JsonResponse
    {
        try {
            $student = Student::findOrFail($studentId);
            $this->authorize('view', $student);

            $data = $this->documentService->getTermBulletinData($studentId, $academicPeriodId);

            return response()->json([
                'data' => $data,
                'message' => 'Données de bulletin récupérées',
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des données du bulletin: " . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des données',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Télécharger le bulletin par trimestre en PDF
     */
    public function downloadTermBulletin(int $studentId, int $academicPeriodId): \Illuminate\Http\Response
    {
        try {
            $student = Student::findOrFail($studentId);
            $this->authorize('view', $student);

            $data = $this->documentService->getTermBulletinData($studentId, $academicPeriodId);

            $filename = sprintf(
                '%s_%s_%s_%s.pdf',
                $student->last_name,
                $student->first_name,
                $data['academic']['term'],
                $data['academic']['year']
            );

            // Générer le PDF
            $pdf = Pdf::loadView('livewire.student-dashboard.bulletins.bulletin-pdf', ['data' => $data])
                ->setOptions(['defaultFont' => 'sans-serif']);

            Log::info("Bulletin trimestrel téléchargé pour l'élève {$studentId}");
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error("Erreur lors du téléchargement du bulletin: " . $e->getMessage());
            abort(500, "Erreur: " . $e->getMessage());
        }
    }

    /**
     * Prévisualiser le bulletin par trimestre
     */
    public function previewTermBulletin(int $studentId, int $academicPeriodId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $this->authorize('view', $student);

            $data = $this->documentService->getTermBulletinData($studentId, $academicPeriodId);

            return view('livewire.student-dashboard.bulletins.bulletin-preview', ['data' => $data]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la prévisualisation du bulletin: " . $e->getMessage());
            abort(500, "Erreur: " . $e->getMessage());
        }
    }

    /**
     * Obtenir le résumé de classe pour un trimestre
     */
    public function getTermClassSummary(int $classId, int $academicPeriodId): JsonResponse
    {
        try {
            $data = $this->documentService->getTermClassSummary($classId, $academicPeriodId);

            return response()->json([
                'data' => $data,
                'message' => 'Résumé de classe récupéré',
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération du résumé de classe: " . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération du résumé',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Télécharger le résumé de classe en PDF
     */
    public function downloadTermClassSummary(int $classId, int $academicPeriodId): \Illuminate\Http\Response
    {
        try {
            $data = $this->documentService->getTermClassSummary($classId, $academicPeriodId);

            $filename = sprintf(
                'class_%s_%s_%s.pdf',
                $classId,
                $data['term'],
                $data['academic_year']
            );

            // Générer le PDF
            $pdf = Pdf::loadView('reports.class-summary-pdf', ['data' => $data])
                ->setOptions(['defaultFont' => 'sans-serif']);

            Log::info("Résumé de classe téléchargé pour la classe {$classId}");
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error("Erreur lors du téléchargement du résumé: " . $e->getMessage());
            abort(500, "Erreur: " . $e->getMessage());
        }
    }

    /**
     * Obtenir le relevé par trimestre
     */
    public function getTermTranscript(int $studentId, Request $request): JsonResponse
    {
        try {
            $student = Student::findOrFail($studentId);
            $this->authorize('view', $student);

            $academicPeriodId = $request->input('academic_period_id');
            $data = $this->documentService->getTermTranscript($studentId, $academicPeriodId);

            return response()->json([
                'data' => $data,
                'message' => 'Relevé récupéré',
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération du relevé: " . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération du relevé',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Télécharger le relevé en PDF
     */
    public function downloadTermTranscript(int $studentId, Request $request): \Illuminate\Http\Response
    {
        try {
            $student = Student::findOrFail($studentId);
            $this->authorize('view', $student);

            $academicPeriodId = $request->input('academic_period_id');
            $data = $this->documentService->getTermTranscript($studentId, $academicPeriodId);

            $filename = sprintf(
                '%s_%s_Transcript_%s.pdf',
                $student->last_name,
                $student->first_name,
                now()->format('Y-m-d')
            );

            // Générer le PDF
            $pdf = Pdf::loadView('reports.term-transcript-pdf', ['data' => $data])
                ->setOptions(['defaultFont' => 'sans-serif']);

            Log::info("Relevé téléchargé pour l'élève {$studentId}");
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error("Erreur lors du téléchargement du relevé: " . $e->getMessage());
            abort(500, "Erreur: " . $e->getMessage());
        }
    }

    /**
     * Lister les trimestres disponibles
     */
    public function getAvailableTerms(Request $request): JsonResponse
    {
        try {
            $academicYear = $request->input('academic_year', now()->year);
            $terms = $this->documentService->getAvailableTerms($academicYear);

            return response()->json([
                'academic_year' => $academicYear,
                'terms' => $terms,
                'total' => count($terms),
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des trimestres: " . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des trimestres',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
