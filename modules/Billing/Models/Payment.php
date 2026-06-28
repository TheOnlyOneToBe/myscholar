<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'student_id',
        'amount',
        'amount_paid',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'validated_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'float',
            'payment_date' => 'datetime',
        ];
    }

    public function getAmountAttribute()
    {
        return $this->amount_paid;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount_paid'] = $value;
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
