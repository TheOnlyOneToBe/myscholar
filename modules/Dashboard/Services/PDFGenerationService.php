<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Modules\Config\Models\SchoolInfo;

class PDFGenerationService
{
    private DocumentGenerationService $documentService;
    private SchoolInfo $schoolInfo;

    public function __construct()
    {
        $this->documentService = app(DocumentGenerationService::class);
        $this->schoolInfo = SchoolInfo::first();
    }

    /**
     * Generate School Certificate PDF HTML
     */
    public function generateSchoolCertificateHTML(Student $student, int $academicYearId): string
    {
        $data = $this->documentService->generateSchoolCertificateData($student, $academicYearId);

        return view('dashboard::documents.school-certificate', [
            'school' => $this->schoolInfo,
            'data' => $data,
        ])->render();
    }

    /**
     * Generate Report Card PDF HTML
     */
    public function generateReportCardHTML(Student $student, int $academicYearId): string
    {
        $data = $this->documentService->generateReportCardData($student, $academicYearId);

        return view('dashboard::documents.report-card', [
            'school' => $this->schoolInfo,
            'data' => $data,
        ])->render();
    }

    /**
     * Generate Transcript PDF HTML
     */
    public function generateTranscriptHTML(Student $student): string
    {
        $data = $this->documentService->generateTranscriptData($student);

        return view('dashboard::documents.transcript', [
            'school' => $this->schoolInfo,
            'data' => $data,
        ])->render();
    }

    /**
     * Generate Enrollment Summary PDF HTML
     */
    public function generateEnrollmentSummaryHTML(Student $student): string
    {
        $data = $this->documentService->generateEnrollmentSummary($student);

        return view('dashboard::documents.enrollment-summary', [
            'school' => $this->schoolInfo,
            'data' => $data,
        ])->render();
    }

    /**
     * Generate Invoice PDF HTML
     */
    public function generateInvoiceHTML(Student $student, string $invoiceId): string
    {
        $data = $this->documentService->generateInvoiceData($student, $invoiceId);

        return view('dashboard::documents.invoice', [
            'school' => $this->schoolInfo,
            'data' => $data,
        ])->render();
    }

    /**
     * Get filename for document
     */
    public function getFilename(string $documentType, Student $student, ?int $academicYearId = null): string
    {
        $studentId = str_pad($student->id, 6, '0', STR_PAD_LEFT);
        $yearPart = $academicYearId ? '_' . $academicYearId : '';

        return match($documentType) {
            'school_certificate' => "Certificate_Scolarite_{$studentId}{$yearPart}_" . date('Ymd') . '.pdf',
            'report_card' => "Bulletin_Scolaire_{$studentId}{$yearPart}_" . date('Ymd') . '.pdf',
            'transcript' => "Releve_Notes_{$studentId}_" . date('Ymd') . '.pdf',
            'enrollment_summary' => "Resume_Inscription_{$studentId}_" . date('Ymd') . '.pdf',
            'invoice' => "Facture_{$studentId}_" . date('Ymd') . '.pdf',
            default => "Document_{$studentId}_" . date('Ymd') . '.pdf',
        };
    }
}
