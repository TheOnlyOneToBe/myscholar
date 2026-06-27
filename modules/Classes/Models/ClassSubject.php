<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSubject extends Model
{
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'hours_per_week',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'hours_per_week' => 'float',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\Modules\Grades\Models\Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class, 'teacher_id');
    }

    public function isActive(): bool
    {
        $today = now()->toDateString();
        return $today >= $this->start_date->toDateString() &&
               $today <= $this->end_date->toDateString();
    }
}
