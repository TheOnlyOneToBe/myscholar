<?php

namespace Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Modules\Dashboard\Services\BulletinPDFService;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinController extends Controller
{
    public function __construct(private BulletinPDFService $bulletinService)
    {
    }

    public function downloadBulletin(string $term = null)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403);
        }

        $data = $this->bulletinService->getBulletinData($student->id, $term);

        if (empty($data)) {
            abort(404, 'Bulletin not found');
        }

        $termName = $data['academic']['term'] ?? 'Bulletin';
        $filename = "{$student->last_name}_{$student->first_name}_{$termName}_{$data['academic']['year']}.pdf";

        $pdf = Pdf::loadView('livewire.student-dashboard.bulletins.bulletin-pdf', ['data' => $data])
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download($filename);
    }

    public function downloadCompleteBulletin()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403);
        }

        $data = $this->bulletinService->getCompleteBulletinData($student->id);

        if (empty($data)) {
            abort(404, 'Complete bulletin not found');
        }

        $year = $data['year'];
        $filename = "{$student->last_name}_{$student->first_name}_Bulletin_Complet_{$year}.pdf";

        $pdf = Pdf::loadView('livewire.student-dashboard.bulletins.complete-bulletin-pdf', ['data' => $data])
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download($filename);
    }

    public function previewBulletin(string $term = null)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403);
        }

        $data = $this->bulletinService->getBulletinData($student->id, $term);

        if (empty($data)) {
            abort(404, 'Bulletin not found');
        }

        return view('livewire.student-dashboard.bulletins.bulletin-preview', ['data' => $data]);
    }

    public function previewCompleteBulletin()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403);
        }

        $data = $this->bulletinService->getCompleteBulletinData($student->id);

        if (empty($data)) {
            abort(404, 'Complete bulletin not found');
        }

        return view('livewire.student-dashboard.bulletins.complete-bulletin-preview', ['data' => $data]);
    }
}
