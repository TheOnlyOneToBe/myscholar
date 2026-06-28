<?php

namespace Modules\Students\Models;

use App\Traits\BelongsToSchoolYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Modules\Students\Enums\EnrollmentStatus;
use Modules\Students\ValueObjects\Email;
use Modules\Students\ValueObjects\Gender;
use Modules\Students\ValueObjects\PhoneNumber;

class Student extends Model
{
    use BelongsToSchoolYear, HasFactory;

    protected $fillable = [
        'user_id',
        'student_id_number',
        'email',
        'phone_number',
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
        'is_chef_classe',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'enrollment_status' => EnrollmentStatus::class,
            'is_chef_classe' => 'boolean',
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->first_name} {$this->last_name}",
        );
    }

    /**
     * Get the user associated with this student
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class);
    }

    /**
     * Get student contacts
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(StudentContact::class);
    }

    /**
     * Get family contacts (parents, guardians, emergency contacts)
     */
    public function familyContacts(): HasMany
    {
        return $this->hasMany(FamilyContact::class);
    }

    /**
     * Get parents/guardians
     */
    public function parents()
    {
        return $this->hasMany(StudentParent::class);
    }

    /**
     * Get enrollments
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Get history
     */
    public function history(): HasMany
    {
        return $this->hasMany(StudentHistory::class);
    }

    /**
     * Get full name
     */
    public function getFullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get age
     */
    public function getAge(): int
    {
        return (int) $this->date_of_birth->diffInYears(now());
    }

    /**
     * Get gender as Gender value object
     */
    public function getGender(): Gender
    {
        return new Gender($this->sex);
    }

    /**
     * Set gender from Gender value object
     */
    public function setGenderFromObject(Gender $gender): self
    {
        $this->sex = $gender->value();
        return $this;
    }

    /**
     * Get email as Email value object
     */
    public function getEmailObject(): ?Email
    {
        if (!$this->email) {
            return null;
        }

        return new Email($this->email);
    }

    /**
     * Set email from Email value object
     */
    public function setEmailFromObject(Email $email): self
    {
        $this->email = $email->value();
        return $this;
    }

    /**
     * Get phone as PhoneNumber value object
     */
    public function getPhoneObject(): ?PhoneNumber
    {
        if (!$this->phone_number) {
            return null;
        }

        return new PhoneNumber($this->phone_number);
    }

    /**
     * Set phone from PhoneNumber value object
     */
    public function setPhoneFromObject(PhoneNumber $phone): self
    {
        $this->phone_number = $phone->isEmpty() ? null : $phone->value();
        return $this;
    }

    /**
     * Check if student is active
     */
    public function isActive(): bool
    {
        return $this->enrollment_status === EnrollmentStatus::ACTIVE;
    }

    /**
     * Check if student can be modified
     */
    public function canModify(): bool
    {
        return $this->enrollment_status->canModify();
    }

    /**
     * Suspend the student
     */
    public function suspend(): void
    {
        if (!$this->canModify()) {
            throw new \InvalidArgumentException(
                trans('students.errors.cannot_modify_status', ['status' => $this->enrollment_status->label()])
            );
        }
        $this->update(['enrollment_status' => EnrollmentStatus::SUSPENDED]);
    }

    /**
     * Reactivate the student
     */
    public function reactivate(): void
    {
        if (!$this->canModify()) {
            throw new \InvalidArgumentException(
                trans('students.errors.cannot_modify_status', ['status' => $this->enrollment_status->label()])
            );
        }
        $this->update(['enrollment_status' => EnrollmentStatus::ACTIVE]);
    }

    /**
     * Withdraw the student
     */
    public function withdraw(): void
    {
        if (!$this->canModify()) {
            throw new \InvalidArgumentException(
                trans('students.errors.cannot_modify_status', ['status' => $this->enrollment_status->label()])
            );
        }
        $this->update(['enrollment_status' => EnrollmentStatus::WITHDRAWN]);
    }

    /**
     * Graduate the student
     */
    public function graduate(): void
    {
        if (!$this->canModify()) {
            throw new \InvalidArgumentException(
                trans('students.errors.cannot_modify_status', ['status' => $this->enrollment_status->label()])
            );
        }
        $this->update(['enrollment_status' => EnrollmentStatus::GRADUATED]);
    }

    /**
     * Defer the student
     */
    public function defer(): void
    {
        if (!$this->canModify()) {
            throw new \InvalidArgumentException(
                trans('students.errors.cannot_modify_status', ['status' => $this->enrollment_status->label()])
            );
        }
        $this->update(['enrollment_status' => EnrollmentStatus::DEFERRED]);
    }

    /**
     * Scope: Get primary contact for student
     */
    public function scopeWithPrimaryContact($query)
    {
        return $query->with(['familyContacts' => function ($q) {
            $q->primary()->first();
        }]);
    }

    /**
     * Scope: Get emergency contacts
     */
    public function scopeWithEmergencyContacts($query)
    {
        return $query->with(['familyContacts' => function ($q) {
            $q->emergency();
        }]);
    }

    /**
     * Get primary family contact
     */
    public function getPrimaryContact(): ?FamilyContact
    {
        return $this->familyContacts()
            ->primary()
            ->first();
    }

    /**
     * Get emergency contacts
     */
    public function getEmergencyContacts()
    {
        return $this->familyContacts()
            ->emergency()
            ->get();
    }

    /**
     * Get the current class
     */
    public function getCurrentClass()
    {
        return \Modules\Classes\Models\SchoolClass::find($this->current_class_id);
    }

    /**
     * Assign student as chef de classe
     */
    public function makeChefClasse(): void
    {
        $this->update(['is_chef_classe' => true]);
        $this->user?->assignRole('chef_classe');
    }

    /**
     * Remove chef de classe status from student
     */
    public function removeChefClasse(): void
    {
        $this->update(['is_chef_classe' => false]);
        $this->user?->removeRole('chef_classe');
    }
}
