<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenceCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_absences',
        'unjustified_absences',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public function getAbsencePercentage(int $totalClassDays): float
    {
        return ($this->total_absences / $totalClassDays) * 100;
    }

    public function isHighRiskAbsence(float $threshold = 15): bool
    {
        return $this->unjustified_absences > 0 && ($this->total_absences / 100) >= $threshold;
    }
}
