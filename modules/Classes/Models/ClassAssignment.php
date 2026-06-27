<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassAssignment extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'school_year_id',
        'assignment_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assignment_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public static function getStatuses(): array
    {
        return ['active', 'transferred', 'suspended', 'withdrawn'];
    }

    public function transfer(SchoolClass $newClass): void
    {
        $this->update(['status' => 'transferred']);
        static::create([
            'student_id' => $this->student_id,
            'class_id' => $newClass->id,
            'school_year_id' => $this->school_year_id,
            'assignment_date' => now()->toDateString(),
            'status' => 'active',
        ]);
    }
}
