<?php

namespace Modules\Billing\Policies;

use Modules\Auth\Models\User;
use Modules\Billing\Models\Invoice;
use Modules\Students\Models\Student;
use Modules\Students\Models\StudentParent;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur', 'enseignant', 'comptable']);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        // Admin roles can view all invoices
        if ($user->hasAnyRole(['super_administrator', 'proviseur', 'censeur', 'comptable'])) {
            return true;
        }

        if ($user->hasRole('enseignant')) {
            return $invoice->student->class_id === $user->class_id;
        }

        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $invoice->student_id === $student->id;
        }

        // Parents can view their child's invoices
        if ($user->hasRole('parent')) {
            return StudentParent::where('parent_user_id', $user->id)
                ->where('student_id', $invoice->student_id)
                ->exists();
        }

        // Chef de classe can view classmates' invoices (read-only)
        if ($user->hasRole('chef_classe')) {
            return $this->viewByClass($user, $invoice);
        }

        return false;
    }

    /**
     * Chef de classe can view classmates' invoices (read-only).
     */
    public function viewByClass(User $user, Invoice $invoice): bool
    {
        if (!$user->hasRole('chef_classe')) {
            return false;
        }

        $userStudent = Student::where('user_id', $user->id)->first();

        if (!$userStudent) {
            return false;
        }

        // Chef de classe must be in the same class
        return $userStudent->current_class_id === $invoice->student->current_class_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur', 'comptable']);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($user->hasAnyRole(['super_administrator', 'proviseur'])) {
            return true;
        }

        if ($user->hasRole('comptable') && !$invoice->isFullyPaid()) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur']) && $invoice->status === 'draft';
    }

    public function markAsOverdue(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur', 'comptable']);
    }

    public function export(User $user): bool
    {
        return $user->hasAnyRole(['super_administrator', 'proviseur', 'comptable']);
    }

    /**
     * Chef de classe cannot download classmates' invoices.
     */
    public function downloadByClass(User $user, Invoice $invoice): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot modify classmates' invoices (read-only enforcement).
     */
    public function modifyByClass(User $user, Invoice $invoice): bool
    {
        return false;
    }

    /**
     * Chef de classe cannot manage classmates' invoices.
     */
    public function manageByClass(User $user, Invoice $invoice): bool
    {
        return false;
    }
}
