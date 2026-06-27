<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeWaiver extends Model
{
    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'amount',
        'percentage',
        'reason',
        'approved_by_user_id',
        'approval_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'percentage' => 'float',
            'approval_date' => 'date',
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

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'approved_by_user_id');
    }

    public static function getStatuses(): array
    {
        return ['pending', 'approved', 'rejected'];
    }

    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'approval_date' => now()->toDateString(),
        ]);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }
}
