<?php

namespace Modules\Dashboard\Services;

use Modules\Students\Models\Student;
use Modules\Students\Models\StudentEnrollment;
use Modules\Grades\Models\Grade;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DocumentGenerationService
{
    /**
     * Generate enrollment history with grades and payments
     */
    public function getEnrollmentHistory(Student $student): array
    {
        if (!Schema::hasTable('student_enrollments')) {
            return [];
        }

        $enrollments = StudentEnrollment::where('student_id', $student->id)
            ->with('class', 'academicYear')
            ->latest('academic_year_id')
            ->get();

        return $enrollments->map(function ($enrollment) use ($student) {
            $grades = Grade::where('student_id', $student->id)
                ->whereYear('created_at', $enrollment->academicYear->year)
                ->get();

            $invoices = Invoice::where('student_id', $student->id)
                ->whereYear('created_at', $enrollment->academicYear->year)
                ->get();

            $payments = Payment::where('student_id', $student->id)
                ->whereYear('created_at', $enrollment->academicYear->year)
                ->get();

            return [
                'id' => $enrollment->id,
                'academic_year' => $enrollment->academicYear->year ?? 'N/A',
                'class' => $enrollment->class->name ?? 'N/A',
                'enrollment_date' => $enrollment->enrollment_date?->format('d/m/Y'),
                'status' => $enrollment->status,
                'average_grade' => round($grades->avg('score') ?? 0, 2),
                'total_grades' => $grades->count(),
                'total_invoiced' => $invoices->sum('total_amount'),
                'total_paid' => $payments->sum('amount'),
                'outstanding' => $invoices->sum('total_amount') - $payments->sum('amount'),
            ];
        })->toArray();
    }

    /**
     * Generate school certificate document data
     */
    public function generateSchoolCertificateData(Student $student, int $academicYearId): array
    {
        if (!Schema::hasTable('student_enrollments')) {
            return [];
        }

        $enrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->with('class', 'academicYear')
            ->first();

        if (!$enrollment) {
            return [];
        }

        return [
            'student_name' => $student->full_name,
            'student_id' => $student->matricule,
            'date_of_birth' => $student->date_of_birth?->format('d/m/Y'),
            'class' => $enrollment->class->name,
            'academic_year' => $enrollment->academicYear->year,
            'enrollment_status' => $enrollment->status,
            'enrollment_date' => $enrollment->enrollment_date?->format('d/m/Y'),
            'document_type' => 'school_certificate',
            'generated_date' => now()->format('d/m/Y'),
            'certificate_number' => $this->generateCertificateNumber($student->id, $academicYearId),
        ];
    }

    /**
     * Generate report card (bulletin) document data
     */
    public function generateReportCardData(Student $student, int $academicYearId): array
    {
        if (!Schema::hasTable('grades')) {
            return [];
        }

        $enrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->with('class', 'academicYear')
            ->first();

        if (!$enrollment) {
            return [];
        }

        $grades = Grade::where('student_id', $student->id)
            ->whereYear('created_at', $enrollment->academicYear->year)
            ->with('subject')
            ->get()
            ->groupBy('subject_id')
            ->map(function ($subjectGrades) {
                $avg = $subjectGrades->avg('score');
                return [
                    'subject' => $subjectGrades->first()->subject->name,
                    'grades' => $subjectGrades->map(fn($g) => $g->score)->toArray(),
                    'average' => round($avg, 2),
                    'grade_letter' => $this->getGradeFromScore($avg),
                ];
            });

        $overall_average = round($grades->avg('average'), 2);

        return [
            'student_name' => $student->full_name,
            'student_id' => $student->matricule,
            'class' => $enrollment->class->name,
            'academic_year' => $enrollment->academicYear->year,
            'subjects' => $grades->toArray(),
            'overall_average' => $overall_average,
            'overall_grade' => $this->getGradeFromScore($overall_average),
            'total_subjects' => $grades->count(),
            'document_type' => 'report_card',
            'generated_date' => now()->format('d/m/Y'),
            'bulletin_number' => $this->generateBulletinNumber($student->id, $academicYearId),
        ];
    }

    /**
     * Generate invoice document data
     */
    public function generateInvoiceData(Student $student, string $invoiceId): array
    {
        if (!Schema::hasTable('invoices')) {
            return [];
        }

        $invoice = Invoice::where('id', $invoiceId)
            ->where('student_id', $student->id)
            ->with('items')
            ->first();

        if (!$invoice) {
            return [];
        }

        return [
            'invoice_number' => $invoice->invoice_number,
            'student_name' => $student->full_name,
            'student_id' => $student->matricule,
            'student_email' => $student->user->email ?? 'N/A',
            'issued_date' => $invoice->issued_date?->format('d/m/Y'),
            'due_date' => $invoice->due_date?->format('d/m/Y'),
            'items' => $invoice->items->map(fn($item) => [
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ])->toArray(),
            'subtotal' => $invoice->subtotal,
            'tax' => $invoice->tax ?? 0,
            'total_amount' => $invoice->total_amount,
            'status' => $invoice->status,
            'payment_terms' => $invoice->payment_terms,
            'notes' => $invoice->notes,
            'document_type' => 'invoice',
            'generated_date' => now()->format('d/m/Y'),
        ];
    }

    /**
     * Generate transcript document data
     */
    public function generateTranscriptData(Student $student): array
    {
        if (!Schema::hasTable('grades')) {
            return [];
        }

        $enrollments = StudentEnrollment::where('student_id', $student->id)
            ->with('academicYear')
            ->latest('academic_year_id')
            ->get();

        $transcript = $enrollments->map(function ($enrollment) use ($student) {
            $grades = Grade::where('student_id', $student->id)
                ->whereYear('created_at', $enrollment->academicYear->year)
                ->with('subject')
                ->get();

            return [
                'academic_year' => $enrollment->academicYear->year,
                'subjects' => $grades->map(fn($g) => [
                    'name' => $g->subject->name,
                    'score' => $g->score,
                    'grade' => $this->getGradeFromScore($g->score),
                ])->toArray(),
                'average' => round($grades->avg('score') ?? 0, 2),
            ];
        });

        return [
            'student_name' => $student->full_name,
            'student_id' => $student->matricule,
            'date_of_birth' => $student->date_of_birth?->format('d/m/Y'),
            'gender' => $student->gender,
            'address' => $student->address,
            'academics' => $transcript->toArray(),
            'overall_average' => round(
                Grade::where('student_id', $student->id)->avg('score') ?? 0,
                2
            ),
            'document_type' => 'transcript',
            'generated_date' => now()->format('d/m/Y'),
            'transcript_number' => $this->generateTranscriptNumber($student->id),
        ];
    }

    /**
     * Generate enrollment summary document
     */
    public function generateEnrollmentSummary(Student $student): array
    {
        $enrollments = StudentEnrollment::where('student_id', $student->id)
            ->with('class', 'academicYear')
            ->latest('academic_year_id')
            ->get();

        $invoices = Invoice::where('student_id', $student->id)->get();
        $payments = Payment::where('student_id', $student->id)->get();

        return [
            'student_name' => $student->full_name,
            'student_id' => $student->matricule,
            'phone' => $student->phone,
            'email' => $student->user->email ?? 'N/A',
            'address' => $student->address,
            'date_of_birth' => $student->date_of_birth?->format('d/m/Y'),
            'gender' => $student->gender,
            'current_class' => $student->getCurrentClass()?->name ?? 'N/A',
            'total_enrollments' => $enrollments->count(),
            'enrollment_years' => $enrollments->pluck('academicYear.year')->toArray(),
            'total_invoiced' => $invoices->sum('total_amount'),
            'total_paid' => $payments->sum('amount'),
            'outstanding_balance' => $invoices->sum('total_amount') - $payments->sum('amount'),
            'document_type' => 'enrollment_summary',
            'generated_date' => now()->format('d/m/Y'),
            'summary_number' => $this->generateSummaryNumber($student->id),
        ];
    }

    /**
     * Get available documents for a student and year
     */
    public function getAvailableDocuments(Student $student, ?int $academicYearId = null): array
    {
        $documents = [];

        if (Schema::hasTable('student_enrollments')) {
            // School Certificate
            $documents[] = [
                'id' => 'school_certificate',
                'name' => 'Certificat de Scolarité',
                'description' => 'Document officiel d\'inscription scolaire',
                'icon' => '📄',
                'available' => true,
            ];

            // Report Card
            $documents[] = [
                'id' => 'report_card',
                'name' => 'Bulletin Scolaire',
                'description' => 'Résultats académiques et moyennes',
                'icon' => '📋',
                'available' => Schema::hasTable('grades'),
            ];

            // Transcript
            $documents[] = [
                'id' => 'transcript',
                'name' => 'Relevé de Notes Complet',
                'description' => 'Historique complet des notes',
                'icon' => '📊',
                'available' => Schema::hasTable('grades'),
            ];

            // Enrollment Summary
            $documents[] = [
                'id' => 'enrollment_summary',
                'name' => 'Résumé d\'Inscription',
                'description' => 'Vue d\'ensemble de la scolarité',
                'icon' => '📑',
                'available' => true,
            ];
        }

        if (Schema::hasTable('invoices')) {
            // Invoices
            $invoices = Invoice::where('student_id', $student->id)
                ->when($academicYearId, fn($q) => $q->whereYear('created_at',
                    DB::table('academic_years')->find($academicYearId)->year ?? null))
                ->get();

            foreach ($invoices as $invoice) {
                $documents[] = [
                    'id' => 'invoice_' . $invoice->id,
                    'name' => 'Facture #' . $invoice->invoice_number,
                    'description' => 'Facture du ' . $invoice->issued_date?->format('d/m/Y'),
                    'icon' => '💰',
                    'available' => true,
                    'invoice_id' => $invoice->id,
                ];
            }
        }

        return $documents;
    }

    /**
     * Private helper methods
     */
    private function generateCertificateNumber(int $studentId, int $academicYearId): string
    {
        return 'CERT-' . date('Y') . '-' . str_pad($studentId, 6, '0', STR_PAD_LEFT) . '-' . str_pad($academicYearId, 4, '0', STR_PAD_LEFT);
    }

    private function generateBulletinNumber(int $studentId, int $academicYearId): string
    {
        return 'BULL-' . date('Y') . '-' . str_pad($studentId, 6, '0', STR_PAD_LEFT) . '-' . str_pad($academicYearId, 4, '0', STR_PAD_LEFT);
    }

    private function generateTranscriptNumber(int $studentId): string
    {
        return 'TRANS-' . date('Y') . '-' . str_pad($studentId, 6, '0', STR_PAD_LEFT);
    }

    private function generateSummaryNumber(int $studentId): string
    {
        return 'SUMM-' . date('Y') . '-' . str_pad($studentId, 6, '0', STR_PAD_LEFT);
    }

    private function getGradeFromScore(float $score): string
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }
}
