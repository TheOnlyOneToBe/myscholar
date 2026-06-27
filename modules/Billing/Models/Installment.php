<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Installment extends Model
{
    protected $fillable = [
        'payment_plan_id',
        'installment_number',
        'amount',
        'due_date',
        'paid_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class);
    }

    public static function getStatuses(): array
    {
        return ['pending', 'paid', 'overdue', 'waived'];
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now()->toDateString(),
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && now()->toDateString() > $this->due_date->toDateString();
    }
}
