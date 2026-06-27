<?php

namespace Modules\Grades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradePeriod extends Model
{
    protected $fillable = [
        'name',
        'school_year_id',
        'grade_entry_start',
        'grade_entry_deadline',
        'publication_date',
    ];

    protected function casts(): array
    {
        return [
            'grade_entry_start' => 'date',
            'grade_entry_deadline' => 'date',
            'publication_date' => 'date',
        ];
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'period_id');
    }

    public function isGradeEntryOpen(): bool
    {
        $now = now()->toDateString();
        return $now >= $this->grade_entry_start->toDateString() &&
               $now <= $this->grade_entry_deadline->toDateString();
    }

    public static function current()
    {
        $today = now()->toDateString();
        return static::where('grade_entry_start', '<=', $today)
            ->where('grade_entry_deadline', '>=', $today)
            ->first();
    }
}
