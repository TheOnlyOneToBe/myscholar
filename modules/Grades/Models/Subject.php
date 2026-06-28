<?php

namespace Modules\Grades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'code',
        'name',
        'description',
        'credits',
        'coefficient',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'coefficient' => 'decimal:2',
    ];

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
