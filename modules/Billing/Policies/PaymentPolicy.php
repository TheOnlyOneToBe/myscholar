<?php

namespace Modules\Billing\Policies;

use Modules\Auth\Models\User;
use Modules\Billing\Models\Payment;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant']);
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasAnyRole(['admin', 'directeur', 'accountant'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $payment->invoice->student->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant']);
    }

    public function record(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant']);
    }

    public function refund(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur']);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin') && now()->diffInHours($payment->created_at) < 24;
    }

    public function export(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'directeur', 'accountant']);
    }
}
