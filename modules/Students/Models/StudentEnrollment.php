<?php

namespace Modules\Students\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Config\Models\SchoolYear;

class StudentEnrollment extends Model
{
    protected $fillable = [
        'student_id',
        'school_year_id',
        'class_id',
        'filiere',
        'level',
        'enrollment_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(\Modules\Classes\Models\SchoolClass::class, 'class_id');
    }

    public function academicPeriods()
    {
        return $this->belongsToMany(
            \Modules\Config\Models\AcademicPeriod::class,
            'enrollment_academic_periods',
            'enrollment_id',
            'academic_period_id'
        )->withTimestamps();
    }

}
