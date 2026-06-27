<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentPlan extends Model
{
    protected $fillable = [
        'invoice_id',
        'student_id',
        'total_installments',
        'installment_amount',
        'frequency',
        'start_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'installment_amount' => 'float',
            'start_date' => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    public static function getFrequencies(): array
    {
        return ['weekly', 'bi_weekly', 'monthly', 'quarterly'];
    }

    public function getCompletedInstallments(): int
    {
        return $this->installments()->where('status', 'paid')->count();
    }

    public function getPendingInstallments(): int
    {
        return $this->installments()->where('status', 'pending')->count();
    }

    public function isCompleted(): bool
    {
        return $this->getCompletedInstallments() === $this->total_installments;
    }
}
