<?php

namespace Modules\Teachers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Classes\Models\SchoolClass;
use Modules\Grades\Models\Subject;
use Modules\Config\Models\SchoolYear;

class TeacherClass extends Model
{
    protected $table = 'teacher_classes';

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'school_year_id',
        'hours_per_week',
        'status',
        'notes',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBySchoolYear($query, $schoolYearId)
    {
        return $query->where('school_year_id', $schoolYearId);
    }
}
