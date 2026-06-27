<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Protects Past School Year Data Trait
 * Prevents modifications to data from past school years without special permission
 * Used for scholarity (academic/billing) related data
 */
trait ProtectsPastSchoolYearData
{
    /**
     * Boot the trait
     */
    public static function bootProtectsPastSchoolYearData(): void
    {
        // Check on update
        static::updating(function (Model $model) {
            return $model->checkPastYearModificationPermission();
        });

        // Check on delete
        static::deleting(function (Model $model) {
            return $model->checkPastYearModificationPermission();
        });
    }

    /**
     * Check if modification is allowed for this record's school year
     */
    private function checkPastYearModificationPermission(): bool
    {
        // If model doesn't have school_year_id, allow
        if (!$this->hasAttribute('school_year_id') || !$this->school_year_id) {
            return true;
        }

        // Get the school year for this record
        if (!method_exists($this, 'schoolYear')) {
            return true;
        }

        $schoolYear = $this->schoolYear;
        if (!$schoolYear) {
            return true;
        }

        // Use the session service to check permission
        $canModify = app(\Modules\Config\Services\SchoolYearSessionService::class)
            ->canModifyYear($schoolYear);

        if (!$canModify) {
            throw new \Illuminate\Database\Eloquent\ModelException(
                trans('config.errors.permission_denied_past_year')
            );
        }

        return true;
    }
}
