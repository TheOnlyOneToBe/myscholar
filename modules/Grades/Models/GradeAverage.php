<?php

namespace Modules\Grades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Students\Models\Student;
use Modules\Config\Models\SchoolYear;

class GradeAverage extends Model
{
    protected $table = 'grade_averages';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'subject_id',
        'grade_period_id',
        'school_year_id',
        'average',
        'rank',
        'is_passed',
    ];

    protected $casts = [
        'average' => 'decimal:2',
        'is_passed' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function gradePeriod(): BelongsTo
    {
        return $this->belongsTo(GradePeriod::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
