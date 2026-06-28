<?php

namespace Modules\Dashboard\Policies;

use Modules\Auth\Models\User;
use Modules\Students\Models\Student;

class DocumentPolicy
{
    /**
     * Determine if the user can download school certificates (their own).
     */
    public function downloadSchoolCertificate(User $user, int $academicYearId, ?Student $student = null): bool
    {
        // If no student provided, check own certificate
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own certificates
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        return false;
    }

    /**
     * Chef de classe cannot download classmates' certificates.
     */
    public function downloadSchoolCertificateByClass(User $user, Student $student): bool
    {
        return false;
    }

    /**
     * Determine if the user can download report cards (their own).
     */
    public function downloadReportCard(User $user, int $academicYearId, ?Student $student = null): bool
    {
        // If no student provided, check own report card
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own report cards
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        return false;
    }

    /**
     * Chef de classe cannot download classmates' report cards.
     */
    public function downloadReportCardByClass(User $user, Student $student): bool
    {
        return false;
    }

    /**
     * Determine if the user can download transcripts (their own).
     */
    public function downloadTranscript(User $user, ?Student $student = null): bool
    {
        // If no student provided, check own transcript
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own transcripts
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        return false;
    }

    /**
     * Chef de classe cannot download classmates' transcripts.
     */
    public function downloadTranscriptByClass(User $user, Student $student): bool
    {
        return false;
    }

    /**
     * Determine if the user can download enrollment summaries (their own).
     */
    public function downloadEnrollmentSummary(User $user, ?Student $student = null): bool
    {
        // If no student provided, check own summary
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own summaries
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        return false;
    }

    /**
     * Chef de classe cannot download classmates' enrollment summaries.
     */
    public function downloadEnrollmentSummaryByClass(User $user, Student $student): bool
    {
        return false;
    }

    /**
     * Determine if the user can download invoices (their own).
     */
    public function downloadInvoice(User $user, int $invoiceId, ?Student $student = null): bool
    {
        // If no student provided, check own invoice
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own invoices
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        return false;
    }

    /**
     * Chef de classe cannot download classmates' invoices.
     */
    public function downloadInvoiceByClass(User $user, Student $student): bool
    {
        return false;
    }

    /**
     * Verify the user owns the student record they're trying to download documents for.
     */
    public function verifyOwnership(User $user, Student $student): bool
    {
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        return false;
    }
}
