<?php

namespace Modules\Dashboard\Policies;

use Modules\Auth\Models\User;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;

class DocumentPolicy
{
    /**
     * Determine if the user can download school certificates.
     */
    public function downloadSchoolCertificate(User $user, int $academicYearId, ?Student $student = null): bool
    {
        // Admin roles can download any student's certificate
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'censeur'])) {
            return true;
        }

        // If no student provided, check own certificate
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own certificates
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        // Parents can download their child's certificate
        if ($user->hasRole('parent')) {
            return $this->isParentOfStudent($user, $student);
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
     * Determine if the user can download report cards.
     */
    public function downloadReportCard(User $user, int $academicYearId, ?Student $student = null): bool
    {
        // Admin roles can download any student's report card
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'censeur'])) {
            return true;
        }

        // If no student provided, check own report card
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own report cards
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        // Parents can download their child's report card
        if ($user->hasRole('parent')) {
            return $this->isParentOfStudent($user, $student);
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
     * Determine if the user can download transcripts.
     */
    public function downloadTranscript(User $user, ?Student $student = null): bool
    {
        // Admin roles can download any student's transcript
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'censeur'])) {
            return true;
        }

        // If no student provided, check own transcript
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own transcripts
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        // Parents can download their child's transcript
        if ($user->hasRole('parent')) {
            return $this->isParentOfStudent($user, $student);
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
     * Determine if the user can download enrollment summaries.
     */
    public function downloadEnrollmentSummary(User $user, ?Student $student = null): bool
    {
        // Admin roles can download any student's enrollment summary
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'censeur'])) {
            return true;
        }

        // If no student provided, check own summary
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own summaries
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        // Parents can download their child's enrollment summary
        if ($user->hasRole('parent')) {
            return $this->isParentOfStudent($user, $student);
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
     * Determine if the user can download invoices.
     */
    public function downloadInvoice(User $user, int $invoiceId, ?Student $student = null): bool
    {
        // Admin roles can download any student's invoice
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'censeur', 'comptable'])) {
            return true;
        }

        // If no student provided, check own invoice
        if (!$student) {
            return $user->hasRole('student');
        }

        // Students can only download their own invoices
        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        // Parents can download their child's invoice
        if ($user->hasRole('parent')) {
            return $this->isParentOfStudent($user, $student);
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
        // Admin can access any student
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'censeur'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            $userStudent = $user->student;
            return $userStudent && $userStudent->id === $student->id;
        }

        // Parent can access their child
        if ($user->hasRole('parent')) {
            return $this->isParentOfStudent($user, $student);
        }

        return false;
    }

    /**
     * Helper method to check if user is parent of a student.
     */
    protected function isParentOfStudent(User $user, Student $student): bool
    {
        return StudentParent::where('parent_user_id', $user->id)
            ->where('student_id', $student->id)
            ->exists();
    }
}
