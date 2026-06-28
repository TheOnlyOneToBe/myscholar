<?php

namespace Modules\Teachers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherQualification extends Model
{
    protected $fillable = [
        'teacher_id',
        'qualification_name',
        'issuing_institution',
        'year_obtained',
        'diploma_file',
        'description',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    /**
     * Get the teacher associated with this qualification
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
