<?php

namespace Modules\Dashboard\Observers;

use Modules\Billing\Models\Invoice;
use Modules\Dashboard\Services\CacheManagementService;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        CacheManagementService::invalidateBillingCache();

        if ($invoice->student) {
            CacheManagementService::invalidateClassCache(
                $invoice->student->getCurrentClass()?->id
            );
        }
    }

    public function updated(Invoice $invoice): void
    {
        CacheManagementService::invalidateBillingCache();

        if ($invoice->student) {
            CacheManagementService::invalidateClassCache(
                $invoice->student->getCurrentClass()?->id
            );
        }
    }

    public function deleted(Invoice $invoice): void
    {
        CacheManagementService::invalidateBillingCache();

        if ($invoice->student) {
            CacheManagementService::invalidateClassCache(
                $invoice->student->getCurrentClass()?->id
            );
        }
    }
}
