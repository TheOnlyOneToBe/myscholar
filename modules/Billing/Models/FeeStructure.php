<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeStructure extends Model
{
    protected $fillable = [
        'name',
        'description',
        'class_id',
        'school_year_id',
        'total_amount',
        'currency',
        'due_date',
        'is_mandatory',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'float',
            'is_mandatory' => 'boolean',
            'due_date' => 'date',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(\Modules\Classes\Models\SchoolClass::class, 'class_id');
    }

    public function getTotalCollected(): float
    {
        return $this->invoices()
            ->where('status', 'paid')
            ->sum('amount_paid');
    }

    public function getCollectionRate(): float
    {
        $studentCount = $this->class->getStudentCount();
        if ($studentCount === 0) {
            return 0;
        }
        $paidCount = $this->invoices()
            ->where('status', 'paid')
            ->distinct('student_id')
            ->count();
        return ($paidCount / $studentCount) * 100;
    }
}
