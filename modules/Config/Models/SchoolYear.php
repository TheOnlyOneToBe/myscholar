<?php

namespace Modules\Config\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_year',
        'end_year',
        'start_date',
        'end_date',
        'is_active',
        'is_locked',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_year' => 'integer',
            'end_year' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    /**
     * Get the active school year
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Get all school years ordered by start_year descending
     */
    public static function allYears()
    {
        return static::orderBy('start_year', 'desc')->get();
    }

    /**
     * Get school year by name (e.g., "2024-2025")
     */
    public static function byName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }

    /**
     * Check if this is the current year
     */
    public function isCurrentYear(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if this year is archived
     */
    public function isArchived(): bool
    {
        return $this->is_locked;
    }

    /**
     * Get the display name
     */
    public function getDisplayName(): string
    {
        return $this->name . ($this->is_active ? ' (En cours)' : '') . ($this->is_locked ? ' (Archivée)' : '');
    }

    /**
     * Check if year data is complete (has start and end dates)
     */
    public function isComplete(): bool
    {
        return $this->start_date && $this->end_date && $this->start_date < $this->end_date;
    }

    /**
     * Get number of days in this school year
     */
    public function getDuration(): int
    {
        if (!$this->isComplete()) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get progress percentage (0-100)
     */
    public function getProgressPercentage(): float
    {
        if (!$this->isComplete()) {
            return 0;
        }

        $today = now()->toDateString();
        if ($today < $this->start_date->toDateString()) {
            return 0;
        }
        if ($today > $this->end_date->toDateString()) {
            return 100;
        }

        $total = $this->getDuration();
        $elapsed = $this->start_date->diffInDays($today);

        return round(($elapsed / $total) * 100, 2);
    }
}
