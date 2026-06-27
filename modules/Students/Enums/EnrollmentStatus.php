<?php

namespace Modules\Students\Enums;

/**
 * Student Enrollment Status Enum
 */
enum EnrollmentStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case WITHDRAWN = 'withdrawn';
    case GRADUATED = 'graduated';
    case DEFERRED = 'deferred';

    /**
     * Get human-readable label (translated)
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => trans('students.enrollment_status.active'),
            self::SUSPENDED => trans('students.enrollment_status.suspended'),
            self::WITHDRAWN => trans('students.enrollment_status.withdrawn'),
            self::GRADUATED => trans('students.enrollment_status.graduated'),
            self::DEFERRED => trans('students.enrollment_status.deferred'),
        };
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => trans('students.enrollment_status_descriptions.active'),
            self::SUSPENDED => trans('students.enrollment_status_descriptions.suspended'),
            self::WITHDRAWN => trans('students.enrollment_status_descriptions.withdrawn'),
            self::GRADUATED => trans('students.enrollment_status_descriptions.graduated'),
            self::DEFERRED => trans('students.enrollment_status_descriptions.deferred'),
        };
    }

    /**
     * Check if student can be modified
     */
    public function canModify(): bool
    {
        return $this !== self::GRADUATED && $this !== self::WITHDRAWN;
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
}
