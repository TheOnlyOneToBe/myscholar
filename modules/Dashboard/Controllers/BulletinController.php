<?php

namespace Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Modules\Dashboard\Services\BulletinPDFService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinController extends Controller
{
    public function __construct(private BulletinPDFService $bulletinService)
    {
    }

    public function downloadBulletin(string $term = null)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->student) {
                Log::warning("Tentative de téléchargement de bulletin sans étudiant authentifié");
                abort(403, 'Accès refusé');
            }

            $student = $user->student;

            // Vérifier que le terme est valide
            $validTerms = [null, 'term_1', 'term_2', 'term_3'];
            if (!in_array($term, $validTerms)) {
                Log::warning("Trimestre invalide: $term pour l'élève {$student->id}");
                abort(400, 'Trimestre invalide');
            }

            $data = $this->bulletinService->getBulletinData($student->id, $term);

            if (empty($data)) {
                Log::info("Aucun bulletin disponible pour {$student->id}, trimestre: $term");
                abort(404, 'Bulletin non disponible');
            }

            $termName = $data['academic']['term'] ?? 'Bulletin';
            $filename = "{$student->last_name}_{$student->first_name}_{$termName}_{$data['academic']['year']}.pdf";

            // Vérifier que la vue existe
            if (!view()->exists('livewire.student-dashboard.bulletins.bulletin-pdf')) {
                Log::error("Vue bulletin-pdf non trouvée");
                abort(500, 'Erreur de configuration');
            }

            $pdf = Pdf::loadView('livewire.student-dashboard.bulletins.bulletin-pdf', ['data' => $data])
                ->setOptions(['defaultFont' => 'sans-serif']);

            Log::info("Bulletin téléchargé pour {$student->id}, trimestre: $term");
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error("Erreur lors du téléchargement du bulletin: " . $e->getMessage());
            abort(500, "Erreur: " . $e->getMessage());
        }
    }

    public function downloadCompleteBulletin()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->student) {
                Log::warning("Tentative de téléchargement du bulletin complet sans étudiant authentifié");
                abort(403, 'Accès refusé');
            }

            $student = $user->student;

            $data = $this->bulletinService->getCompleteBulletinData($student->id);

            if (empty($data)) {
                Log::info("Aucun bulletin complet disponible pour {$student->id}");
                abort(404, 'Bulletin complet non disponible');
            }

            // Vérifier qu'il y a au moins un trimestre avec des données
            $hasTermData = collect($data)
                ->filter(fn($v, $k) => strpos($k, 'term_') === 0)
                ->filter(fn($v) => !empty($v))
                ->isNotEmpty();

            if (!$hasTermData) {
                Log::info("Aucune donnée de trimestre pour {$student->id}");
                abort(404, 'Aucune donnée académique disponible');
            }

            $year = $data['year'];
            $filename = "{$student->last_name}_{$student->first_name}_Bulletin_Complet_{$year}.pdf";

            // Vérifier que la vue existe
            if (!view()->exists('livewire.student-dashboard.bulletins.complete-bulletin-pdf')) {
                Log::error("Vue complete-bulletin-pdf non trouvée");
                abort(500, 'Erreur de configuration');
            }

            $pdf = Pdf::loadView('livewire.student-dashboard.bulletins.complete-bulletin-pdf', ['data' => $data])
                ->setOptions(['defaultFont' => 'sans-serif']);

            Log::info("Bulletin complet téléchargé pour {$student->id}");
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error("Erreur lors du téléchargement du bulletin complet: " . $e->getMessage());
            abort(500, "Erreur: " . $e->getMessage());
        }
    }

    public function previewBulletin(string $term = null)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->student) {
                Log::warning("Tentative de prévisualisation de bulletin sans étudiant authentifié");
                abort(403, 'Accès refusé');
            }

            $student = $user->student;

            // Vérifier que le terme est valide
            $validTerms = [null, 'term_1', 'term_2', 'term_3'];
            if (!in_array($term, $validTerms)) {
                abort(400, 'Trimestre invalide');
            }

            $data = $this->bulletinService->getBulletinData($student->id, $term);

            if (empty($data)) {
                Log::info("Aucun bulletin à prévisualiser pour {$student->id}, trimestre: $term");
                abort(404, 'Bulletin non disponible');
            }

            Log::info("Bulletin prévisualisé pour {$student->id}, trimestre: $term");
            return view('livewire.student-dashboard.bulletins.bulletin-preview', ['data' => $data]);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la prévisualisation du bulletin: " . $e->getMessage());
            abort(500, "Erreur: " . $e->getMessage());
        }
    }

    public function previewCompleteBulletin()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->student) {
                Log::warning("Tentative de prévisualisation du bulletin complet sans étudiant authentifié");
                abort(403, 'Accès refusé');
            }

            $student = $user->student;

            $data = $this->bulletinService->getCompleteBulletinData($student->id);

            if (empty($data)) {
                Log::info("Aucun bulletin complet à prévisualiser pour {$student->id}");
                abort(404, 'Bulletin complet non disponible');
            }

            // Vérifier qu'il y a au moins un trimestre avec des données
            $hasTermData = collect($data)
                ->filter(fn($v, $k) => strpos($k, 'term_') === 0)
                ->filter(fn($v) => !empty($v))
                ->isNotEmpty();

            if (!$hasTermData) {
                Log::info("Aucune donnée de trimestre à prévisualiser pour {$student->id}");
                abort(404, 'Aucune donnée académique disponible');
            }

            Log::info("Bulletin complet prévisualisé pour {$student->id}");
            return view('livewire.student-dashboard.bulletins.complete-bulletin-preview', ['data' => $data]);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la prévisualisation du bulletin complet: " . $e->getMessage());
            abort(500, "Erreur: " . $e->getMessage());
        }
    }
}
