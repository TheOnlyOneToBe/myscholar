<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scholarship extends Model
{
    protected $fillable = [
        'student_id',
        'school_year_id',
        'type',
        'amount',
        'percentage',
        'currency',
        'start_date',
        'end_date',
        'reason',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'percentage' => 'float',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public static function getTypes(): array
    {
        return ['full', 'partial', 'merit', 'need_based', 'special'];
    }

    public static function getStatuses(): array
    {
        return ['pending', 'approved', 'active', 'suspended', 'completed'];
    }

    public function isActive(): bool
    {
        $today = now()->toDateString();
        return $this->status === 'active' &&
               $today >= $this->start_date->toDateString() &&
               $today <= $this->end_date->toDateString();
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function resume(): void
    {
        $this->update(['status' => 'active']);
    }
}
