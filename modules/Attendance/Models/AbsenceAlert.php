<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenceAlert extends Model
{
    protected $fillable = [
        'student_id',
        'absence_counter_id',
        'alert_level',
        'message',
        'sent_at',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public function absenceCounter(): BelongsTo
    {
        return $this->belongsTo(AbsenceCounter::class);
    }

    public static function getLevels(): array
    {
        return ['warning', 'critical', 'suspension'];
    }

    public function acknowledge(): void
    {
        $this->update(['acknowledged_at' => now()]);
    }

    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }
}
