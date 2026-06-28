<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsenceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'reason',
        'absence_threshold',
        'is_acknowledged',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'acknowledged_at' => 'datetime',
            'is_acknowledged' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Students\Models\Student::class);
    }

    public function acknowledge(): void
    {
        $this->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);
    }

    public function isAcknowledged(): bool
    {
        return $this->is_acknowledged;
    }
}
