<?php

namespace Modules\Students\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'student_id_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'sex',
        'place_of_birth',
        'id_number',
        'photo_url',
        'current_class_id',
        'current_filiere',
        'enrollment_status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'enrollment_status' => 'string',
        ];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(StudentContact::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(StudentHistory::class);
    }

    public function getFullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAge(): int
    {
        return $this->date_of_birth->diffInYears(now());
    }

    public function isActive(): bool
    {
        return $this->enrollment_status === 'active';
    }

    public function suspend(): void
    {
        $this->update(['enrollment_status' => 'suspended']);
    }

    public function reactivate(): void
    {
        $this->update(['enrollment_status' => 'active']);
    }

    public function withdraw(): void
    {
        $this->update(['enrollment_status' => 'withdrawn']);
    }

    public function graduate(): void
    {
        $this->update(['enrollment_status' => 'graduated']);
    }
}
