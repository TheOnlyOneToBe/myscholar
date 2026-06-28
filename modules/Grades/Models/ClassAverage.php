<?php

namespace Modules\Grades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Classes\Models\ClassModel;
use Modules\Config\Models\SchoolYear;

class ClassAverage extends Model
{
    protected $table = 'class_averages';

    protected $fillable = [
        'class_id',
        'subject_id',
        'grade_period_id',
        'school_year_id',
        'average',
        'highest_score',
        'lowest_score',
        'pass_rate',
    ];

    protected $casts = [
        'average' => 'decimal:2',
        'highest_score' => 'decimal:2',
        'lowest_score' => 'decimal:2',
        'pass_rate' => 'decimal:2',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class);
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
