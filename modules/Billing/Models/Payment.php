<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'currency',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'processed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'payment_date' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'processed_by_user_id');
    }

    public static function getPaymentMethods(): array
    {
        return ['cash', 'check', 'bank_transfer', 'credit_card', 'mobile_money', 'other'];
    }

    public function recordPayment(): void
    {
        $invoice = $this->invoice;
        $newAmountPaid = $invoice->amount_paid + $this->amount;

        if ($newAmountPaid >= $invoice->amount) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'status' => $status,
        ]);
    }
}
