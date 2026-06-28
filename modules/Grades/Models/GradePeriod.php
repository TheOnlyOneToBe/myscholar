<?php

namespace Modules\Grades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Config\Models\SchoolYear;

class GradePeriod extends Model
{
    protected $table = 'grade_periods';

    protected $fillable = [
        'school_year_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function gradeAverages(): HasMany
    {
        return $this->hasMany(GradeAverage::class);
    }

    public function classAverages(): HasMany
    {
        return $this->hasMany(ClassAverage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
