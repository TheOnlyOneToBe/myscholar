<?php

namespace Modules\Students\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Students\Enums\RelationshipType;
use Modules\Students\ValueObjects\Email;
use Modules\Students\ValueObjects\Gender;
use Modules\Students\ValueObjects\PhoneNumber;

class FamilyContact extends Model
{
    protected $fillable = [
        'student_id',
        'relationship',
        'first_name',
        'last_name',
        'sex',
        'email',
        'phone_number',
        'occupation',
        'address',
        'city',
        'postal_code',
        'is_primary_contact',
        'is_emergency_contact',
    ];

    protected function casts(): array
    {
        return [
            'relationship' => RelationshipType::class,
            'is_primary_contact' => 'boolean',
            'is_emergency_contact' => 'boolean',
        ];
    }

    /**
     * Get the student this contact belongs to
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get full name
     */
    public function getFullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get relationship label (translated)
     */
    public function getRelationshipLabel(): string
    {
        return $this->relationship->label();
    }

    /**
     * Get gender as Gender value object
     */
    public function getGender(): ?Gender
    {
        if (!$this->sex) {
            return null;
        }

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
     * Set email from Email value object
     */
    public function setEmailFromObject(Email $email): self
    {
        $this->email = $email->value();
        return $this;
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
     * Scope: Get primary contacts
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary_contact', true);
    }

    /**
     * Scope: Get emergency contacts
     */
    public function scopeEmergency($query)
    {
        return $query->where('is_emergency_contact', true);
    }

    /**
     * Scope: Get by relationship type
     */
    public function scopeByRelationship($query, RelationshipType $type)
    {
        return $query->where('relationship', $type->value);
    }

    /**
     * Scope: Get parent contacts (father, mother, guardian)
     */
    public function scopeParents($query)
    {
        return $query->whereIn('relationship', [
            RelationshipType::FATHER->value,
            RelationshipType::MOTHER->value,
            RelationshipType::GUARDIAN->value,
        ]);
    }

    /**
     * Mark as primary contact
     */
    public function markAsPrimary(): void
    {
        // Unmark other primary contacts for this student
        FamilyContact::where('student_id', $this->student_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary_contact' => false]);

        $this->update(['is_primary_contact' => true]);
    }

    /**
     * Mark as emergency contact
     */
    public function markAsEmergencyContact(): void
    {
        $this->update(['is_emergency_contact' => true]);
    }
}
