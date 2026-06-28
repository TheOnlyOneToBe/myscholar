<?php

namespace Modules\Config\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AcademicPeriod extends Model
{
    use HasFactory;
    protected $table = 'academic_periods';

    protected $fillable = [
        'name',
        'type',
        'start_date',
        'end_date',
        'academic_year',
        'order',
        'is_active',
        'status',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'academic_year' => 'integer',
    ];

    public function scopeByYear(Builder $query, int $year): Builder
    {
        return $query->where('academic_year', $year);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('academic_year')->orderBy('order');
    }

    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->where('academic_year', now()->year);
    }

    public function isInProgress(): bool
    {
        $now = now();
        return $now->between(
            Carbon::parse($this->start_date),
            Carbon::parse($this->end_date)
        );
    }

    public function isUpcoming(): bool
    {
        return now() < Carbon::parse($this->start_date);
    }

    public function isCompleted(): bool
    {
        return now() > Carbon::parse($this->end_date);
    }

    public function getDaysUntilStart(): int
    {
        return now()->diffInDays(Carbon::parse($this->start_date));
    }

    public function getDaysRemaining(): int
    {
        return Carbon::parse($this->end_date)->diffInDays(now());
    }

    public function getDuration(): int
    {
        return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date));
    }

    public function getStatus(): string
    {
        if ($this->isInProgress()) {
            return 'in_progress';
        }
        if ($this->isCompleted()) {
            return 'completed';
        }
        return 'upcoming';
    }
}
