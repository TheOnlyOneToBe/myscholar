<?php

namespace Modules\Dashboard\Controllers;

use Modules\Dashboard\Services\DocumentGenerationService;
use Modules\Dashboard\Services\PDFGenerationService;
use Modules\Dashboard\Policies\DocumentPolicy;
use Modules\Students\Models\Student;
use App\Traits\VerifiesModuleAccess;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DocumentDownloadController extends Controller
{
    use VerifiesModuleAccess;

    public function __construct(
        private DocumentGenerationService $documentService,
        private PDFGenerationService $pdfService
    ) {}

    /**
     * Download school certificate
     */
    public function schoolCertificate(int $academicYearId): Response
    {
        $user = Auth::user();

        // Allow specifying a student_id for parents/admin/censeur
        $studentId = request()->query('student_id');

        if ($studentId) {
            // Admin roles, parents can download for other students
            $student = Student::find($studentId);
        } else {
            // Get own student record
            $student = Student::where('user_id', $user->id)->first();
        }

        if (!$student) {
            abort(404, 'Student not found');
        }

        if ($error = $this->verifyModuleAccess('Students')) {
            return $error;
        }

        // Verify authorization
        if (!Gate::allows('downloadSchoolCertificate', [$academicYearId, $student])) {
            abort(403, 'Unauthorized - You cannot download this certificate');
        }

        try {
            $html = $this->pdfService->generateSchoolCertificateHTML($student, $academicYearId);
            $filename = $this->pdfService->getFilename('school_certificate', $student, $academicYearId);

            return $this->generatePDFResponse($html, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Document Generation Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download report card
     */
    public function reportCard(int $academicYearId): Response
    {
        $user = Auth::user();

        // Allow specifying a student_id for parents/admin/censeur
        $studentId = request()->query('student_id');

        if ($studentId) {
            $student = Student::find($studentId);
        } else {
            $student = Student::where('user_id', $user->id)->first();
        }

        if (!$student) {
            abort(404, 'Student not found');
        }

        if ($error = $this->verifyModuleAccess('Grades')) {
            return $error;
        }

        // Verify authorization
        if (!Gate::allows('downloadReportCard', [$academicYearId, $student])) {
            abort(403, 'Unauthorized - You cannot download this report card');
        }

        try {
            $html = $this->pdfService->generateReportCardHTML($student, $academicYearId);
            $filename = $this->pdfService->getFilename('report_card', $student, $academicYearId);

            return $this->generatePDFResponse($html, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Document Generation Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download transcript
     */
    public function transcript(): Response
    {
        $user = Auth::user();

        // Allow specifying a student_id for parents/admin/censeur
        $studentId = request()->query('student_id');

        if ($studentId) {
            $student = Student::find($studentId);
        } else {
            $student = Student::where('user_id', $user->id)->first();
        }

        if (!$student) {
            abort(404, 'Student not found');
        }

        if ($error = $this->verifyModuleAccess('Grades')) {
            return $error;
        }

        // Verify authorization
        if (!Gate::allows('downloadTranscript', $student)) {
            abort(403, 'Unauthorized - You cannot download this transcript');
        }

        try {
            $html = $this->pdfService->generateTranscriptHTML($student);
            $filename = $this->pdfService->getFilename('transcript', $student);

            return $this->generatePDFResponse($html, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Document Generation Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download enrollment summary
     */
    public function enrollmentSummary(): Response
    {
        $user = Auth::user();

        // Allow specifying a student_id for parents/admin/censeur
        $studentId = request()->query('student_id');

        if ($studentId) {
            $student = Student::find($studentId);
        } else {
            $student = Student::where('user_id', $user->id)->first();
        }

        if (!$student) {
            abort(404, 'Student not found');
        }

        if ($error = $this->verifyModuleAccess('Students')) {
            return $error;
        }

        // Verify authorization
        if (!Gate::allows('downloadEnrollmentSummary', $student)) {
            abort(403, 'Unauthorized - You cannot download this enrollment summary');
        }

        try {
            $html = $this->pdfService->generateEnrollmentSummaryHTML($student);
            $filename = $this->pdfService->getFilename('enrollment_summary', $student);

            return $this->generatePDFResponse($html, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Document Generation Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download invoice
     */
    public function invoice(string $invoiceId): Response
    {
        $user = Auth::user();

        // Allow specifying a student_id for parents/admin/censeur
        $studentId = request()->query('student_id');

        if ($studentId) {
            $student = Student::find($studentId);
        } else {
            $student = Student::where('user_id', $user->id)->first();
        }

        if (!$student) {
            abort(404, 'Student not found');
        }

        if ($error = $this->verifyModuleAccess('Billing')) {
            return $error;
        }

        // Verify authorization
        if (!Gate::allows('downloadInvoice', [$invoiceId, $student])) {
            abort(403, 'Unauthorized - You cannot download this invoice');
        }

        try {
            $html = $this->pdfService->generateInvoiceHTML($student, $invoiceId);
            $filename = $this->pdfService->getFilename('invoice', $student);

            return $this->generatePDFResponse($html, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Document Generation Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate PDF Response
     * Note: This uses mPDF library (to be installed via composer)
     */
    private function generatePDFResponse(string $html, string $filename): Response
    {
        // For now, return HTML. In production, use mPDF or DomPDF
        // composer require mpdf/mpdf
        // or
        // composer require barryvdh/laravel-dompdf

        try {
            // Using HTML2PDF or mPDF would go here
            // For this implementation, we're setting up the structure

            return response($html)
                ->header('Content-Type', 'text/html; charset=utf-8')
                ->header('Content-Disposition', "inline; filename=\"{$filename}\"");

            // In production with mPDF:
            // $mpdf = new \Mpdf\Mpdf();
            // $mpdf->WriteHTML($html);
            // return response($mpdf->Output($filename, 'D'))
            //     ->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'PDF Generation Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
