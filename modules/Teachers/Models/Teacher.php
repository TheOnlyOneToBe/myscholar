<?php

namespace Modules\Teachers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Models\User;
use Modules\Grades\Models\Subject;
use Modules\Classes\Models\SchoolClass;
use Modules\Config\Models\SchoolYear;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'teacher_code',
        'specialization',
        'qualification_level',
        'hire_date',
        'filiere',
        'office_location',
        'years_of_experience',
        'is_active',
        'bio',
        'phone_office',
        'email_office',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'filiere' => 'string',
    ];

    /**
     * Get the user associated with this teacher
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all qualifications for this teacher
     */
    public function qualifications(): HasMany
    {
        return $this->hasMany(TeacherQualification::class);
    }

    /**
     * Get all subjects taught by this teacher
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects')
            ->withPivot(['proficiency_level', 'since_year', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get all classes this teacher teaches
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'teacher_classes')
            ->withPivot(['subject_id', 'school_year_id', 'hours_per_week', 'status'])
            ->withTimestamps();
    }

    /**
     * Get all history records for this teacher
     */
    public function history(): HasMany
    {
        return $this->hasMany(TeacherHistory::class);
    }

    /**
     * Get primary specialization
     */
    public function primarySubject(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects')
            ->wherePivot('is_primary', true);
    }

    /**
     * Check if teacher is assigned to a class
     */
    public function isAssignedToClass(SchoolClass $class): bool
    {
        return $this->classes()->where('class_id', $class->id)->exists();
    }

    /**
     * Get total hours per week
     */
    public function getTotalHoursPerWeek(): int
    {
        return $this->classes()
            ->sum('teacher_classes.hours_per_week');
    }

    /**
     * Get active classes for current school year
     */
    public function getActiveClasses(?SchoolYear $schoolYear = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->classes()
            ->wherePivot('status', 'active');

        if ($schoolYear) {
            $query->wherePivot('school_year_id', $schoolYear->id);
        }

        return $query->get();
    }

    /**
     * Scope: Filter active teachers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by filiere
     */
    public function scopeByFiliere($query, string $filiere)
    {
        return $query->where('filiere', $filiere);
    }

    /**
     * Scope: Filter by specialization
     */
    public function scopeBySpecialization($query, string $specialization)
    {
        return $query->where('specialization', $specialization);
    }
}
