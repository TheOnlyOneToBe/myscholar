<?php

namespace Modules\Billing\Policies;

use Modules\Auth\Models\User;
use Modules\Billing\Models\Invoice;
use Modules\Students\Models\Student;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'enseignant', 'accountant']);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->hasAnyRole(['admin', 'directeur', 'accountant'])) {
            return true;
        }

        if ($user->hasRole('enseignant')) {
            return $invoice->student->class_id === $user->class_id;
        }

        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            return $student && $invoice->student_id === $student->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant']);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($user->hasAnyRole(['admin', 'directeur'])) {
            return true;
        }

        if ($user->hasRole('accountant') && !$invoice->isFullyPaid()) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['admin', 'directeur']) && $invoice->status === 'draft';
    }

    public function markAsOverdue(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant']);
    }

    public function export(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant']);
    }
}
