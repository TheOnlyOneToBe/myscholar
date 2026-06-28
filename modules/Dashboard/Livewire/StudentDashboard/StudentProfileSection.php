<?php

namespace Modules\Dashboard\Livewire\StudentDashboard;

use Livewire\Component;
use Modules\Dashboard\Services\StudentDashboardService;
use Modules\Dashboard\Services\DocumentGenerationService;
use App\Services\ModuleManager;
use Modules\Students\Models\Student;

class StudentProfileSection extends Component
{
    public $studentInfo = [];
    public $enrollmentHistory = [];
    public $availableDocuments = [];
    public $activeTab = 'profile';
    public $moduleAvailable = false;
    public $moduleError = '';
    public $selectedYear = null;

    public function mount(): void
    {
        $this->checkModuleAvailability();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function filterByYear(?int $year): void
    {
        $this->selectedYear = $year;
        $this->loadAvailableDocuments();
    }

    private function checkModuleAvailability(): void
    {
        $moduleManager = app(ModuleManager::class);

        if (!$moduleManager->canUseModule('Students')) {
            $this->moduleAvailable = false;
            $this->moduleError = $moduleManager->getModuleError('Students');
            return;
        }

        $user = auth()->user();
        if (!$user || !$user->hasRole('student')) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Permission denied';
            return;
        }

        $this->moduleAvailable = true;
        $this->loadProfileData();
    }

    private function loadProfileData(): void
    {
        try {
            $service = app(StudentDashboardService::class);
            $this->studentInfo = $service->getStudentInfo();

            $student = Student::where('user_id', auth()->user()->id)->first();
            if ($student) {
                $docService = app(DocumentGenerationService::class);
                $this->enrollmentHistory = $docService->getEnrollmentHistory($student);
                $this->loadAvailableDocuments();
            }
        } catch (\Exception $e) {
            $this->moduleAvailable = false;
            $this->moduleError = 'Error loading profile: ' . $e->getMessage();
        }
    }

    private function loadAvailableDocuments(): void
    {
        try {
            $student = Student::where('user_id', auth()->user()->id)->first();
            if ($student) {
                $docService = app(DocumentGenerationService::class);
                $this->availableDocuments = $docService->getAvailableDocuments($student, $this->selectedYear);
            }
        } catch (\Exception $e) {
            \Log::error('Error loading documents: ' . $e->getMessage());
        }
    }

    public function downloadDocument(string $documentType, ?string $invoiceId = null): void
    {
        $student = Student::where('user_id', auth()->user()->id)->first();
        if (!$student) {
            $this->dispatch('alert', 'error', 'Student not found');
            return;
        }

        try {
            // Here we would trigger the download
            // This will be handled by the controller
            $this->dispatch('download', [
                'type' => $documentType,
                'studentId' => $student->id,
                'invoiceId' => $invoiceId,
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', 'error', 'Error generating document: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('dashboard::livewire.student-dashboard.student-profile-section');
    }
}
