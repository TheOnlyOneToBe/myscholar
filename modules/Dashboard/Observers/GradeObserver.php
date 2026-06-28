<?php

namespace Modules\Dashboard\Observers;

use Modules\Grades\Models\Grade;
use Modules\Dashboard\Services\CacheManagementService;

class GradeObserver
{
    /**
     * Handle the Grade "created" event.
     */
    public function created(Grade $grade): void
    {
        // Invalider le cache de l'élève quand une note est créée
        CacheManagementService::invalidateGradeCache();

        // Invalider le cache de la classe aussi
        if ($grade->student) {
            CacheManagementService::invalidateClassCache(
                $grade->student->getCurrentClass()?->id
            );
        }
    }

    /**
     * Handle the Grade "updated" event.
     */
    public function updated(Grade $grade): void
    {
        // Invalider le cache quand une note est modifiée
        CacheManagementService::invalidateGradeCache();

        if ($grade->student) {
            CacheManagementService::invalidateClassCache(
                $grade->student->getCurrentClass()?->id
            );
        }
    }

    /**
     * Handle the Grade "deleted" event.
     */
    public function deleted(Grade $grade): void
    {
        CacheManagementService::invalidateGradeCache();

        if ($grade->student) {
            CacheManagementService::invalidateClassCache(
                $grade->student->getCurrentClass()?->id
            );
        }
    }
}
