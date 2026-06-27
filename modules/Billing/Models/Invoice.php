<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'student_id',
        'fee_structure_id',
        'amount',
        'amount_paid',
        'currency',
        'issue_date',
        'due_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'amount_paid' => 'float',
            'issue_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public static function getStatuses(): array
    {
        return ['draft', 'issued', 'partial', 'paid', 'overdue', 'cancelled'];
    }

    public function getRemainingAmount(): float
    {
        return $this->amount - $this->amount_paid;
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || (now()->toDateString() > $this->due_date->toDateString() && $this->status !== 'paid');
    }

    public function isFullyPaid(): bool
    {
        return abs($this->amount - $this->amount_paid) < 0.01;
    }

    public function markAsOverdue(): void
    {
        if (!$this->isFullyPaid() && $this->isOverdue()) {
            $this->update(['status' => 'overdue']);
        }
    }
}
