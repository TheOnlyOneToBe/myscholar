<?php

namespace Modules\Students\Enums;

/**
 * Family Relationship Type Enum
 */
enum RelationshipType: string
{
    case FATHER = 'father';
    case MOTHER = 'mother';
    case GUARDIAN = 'guardian';
    case EMERGENCY_CONTACT = 'emergency_contact';
    case SIBLING = 'sibling';
    case GRANDPARENT = 'grandparent';
    case UNCLE = 'uncle';
    case AUNT = 'aunt';
    case COUSIN = 'cousin';
    case OTHER = 'other';

    /**
     * Get human-readable label (translated)
     */
    public function label(): string
    {
        return match ($this) {
            self::FATHER => trans('students.relationships.father'),
            self::MOTHER => trans('students.relationships.mother'),
            self::GUARDIAN => trans('students.relationships.guardian'),
            self::EMERGENCY_CONTACT => trans('students.relationships.emergency_contact'),
            self::SIBLING => trans('students.relationships.sibling'),
            self::GRANDPARENT => trans('students.relationships.grandparent'),
            self::UNCLE => trans('students.relationships.uncle'),
            self::AUNT => trans('students.relationships.aunt'),
            self::COUSIN => trans('students.relationships.cousin'),
            self::OTHER => trans('students.relationships.other'),
        };
    }

    /**
     * Check if this is a parent relationship
     */
    public function isParent(): bool
    {
        return in_array($this, [self::FATHER, self::MOTHER, self::GUARDIAN]);
    }

    /**
     * Check if this is an emergency contact
     */
    public function isEmergencyContact(): bool
    {
        return $this === self::EMERGENCY_CONTACT;
    }

    /**
     * Get all options for select
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    /**
     * Get parent options only
     */
    public static function parentOptions(): array
    {
        return collect(self::cases())
            ->filter(fn ($case) => $case->isParent())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
