<?php

namespace Modules\Dashboard\Livewire\ParentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\DocumentGenerationService;
use Modules\Students\Models\Student;
use Modules\Config\Models\SchoolYear;

class ParentDocumentsSection extends Component
{
    public $selectedChildId = null;
    public $childName = '';
    public $availableDocuments = [];
    public $academicYears = [];
    public $selectedYear = null;

    protected $listeners = ['childSelected' => 'updateSelectedChild'];

    public function mount(?int $childId = null): void
    {
        if ($childId) {
            $this->selectedChildId = $childId;
        }
        $this->loadDocuments();
    }

    public function updateSelectedChild(int $childId): void
    {
        $this->selectedChildId = $childId;
        $this->loadDocuments();
    }

    private function loadDocuments(): void
    {
        if (!$this->selectedChildId) {
            return;
        }

        $user = auth()->user();

        if (!$user || !$user->hasRole('parent')) {
            return;
        }

        try {
            $student = Student::find($this->selectedChildId);

            if (!$student) {
                return;
            }

            $this->childName = $student->full_name;
            $service = app(DocumentGenerationService::class);

            $this->availableDocuments = $service->getAvailableDocuments($student, $this->selectedYear);
            $this->academicYears = SchoolYear::orderBy('year', 'desc')
                ->limit(5)
                ->pluck('year')
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Error loading parent documents: ' . $e->getMessage());
        }
    }

    public function selectYear(int $year): void
    {
        $this->selectedYear = $year;
        $this->loadDocuments();
    }

    public function downloadDocument(string $documentType, ?int $academicYearId = null): void
    {
        if (!$this->selectedChildId) {
            $this->dispatch('error', 'No child selected');
            return;
        }

        try {
            $student = Student::find($this->selectedChildId);

            if (!$student) {
                $this->dispatch('error', 'Student not found');
                return;
            }

            $redirectUrl = match ($documentType) {
                'school_certificate' => route('dashboard.documents.certificate', [
                    'academicYearId' => $academicYearId ?? $this->selectedYear ?? now()->year,
                ]) . '?student_id=' . $this->selectedChildId,
                'report_card' => route('dashboard.documents.report-card', [
                    'academicYearId' => $academicYearId ?? $this->selectedYear ?? now()->year,
                ]) . '?student_id=' . $this->selectedChildId,
                'transcript' => route('dashboard.documents.transcript') . '?student_id=' . $this->selectedChildId,
                'enrollment_summary' => route('dashboard.documents.enrollment-summary') . '?student_id=' . $this->selectedChildId,
                default => null,
            };

            if (!$redirectUrl) {
                $this->dispatch('error', 'Unknown document type');
                return;
            }

            $this->redirect($redirectUrl);
        } catch (\Exception $e) {
            \Log::error('Error downloading document: ' . $e->getMessage());
            $this->dispatch('error', 'Error downloading document');
        }
    }

    public function render()
    {
        return view('dashboard::livewire.parent-dashboard.parent-documents-section');
    }
}
