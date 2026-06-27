<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenceCounter extends Model
{
    protected $fillable = [
        'student_id',
        'school_year_id',
        'total_absences',
        'total_justified',
        'total_unjustified',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public function incrementAbsence(bool $justified = false): void
    {
        $this->increment('total_absences');
        if ($justified) {
            $this->increment('total_justified');
        } else {
            $this->increment('total_unjustified');
        }
    }

    public function getAbsencePercentage(int $totalClassDays): float
    {
        return ($this->total_absences / $totalClassDays) * 100;
    }

    public function isHighRiskAbsence(float $threshold = 15): bool
    {
        return $this->total_unjustified > 0 && ($this->total_absences / 100) >= $threshold;
    }
}
