<?php

namespace Modules\Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'code',
        'name',
        'building',
        'floor',
        'capacity',
        'room_type',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public static function getRoomTypes(): array
    {
        return ['classroom', 'laboratory', 'library', 'computer_lab', 'multipurpose'];
    }

    public function isAvailable(string $dayOfWeek, string $startTime, string $endTime): bool
    {
        return !$this->timetables()
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->exists();
    }
}
