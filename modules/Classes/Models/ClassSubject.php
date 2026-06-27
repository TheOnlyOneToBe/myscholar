<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Config\Models\SchoolYear;

class ClassSubject extends Model
{
    protected $fillable = [
        'class_id',
        'subject_code',
        'subject_name',
        'hours_per_week',
        'school_year_id',
        'is_optional',
        'is_active',
    ];

    protected $casts = [
        'hours_per_week' => 'integer',
        'is_optional' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
