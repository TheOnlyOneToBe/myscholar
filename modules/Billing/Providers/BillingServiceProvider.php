<?php

namespace Modules\Billing\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Billing\Services\BillingService;
use Modules\Billing\Services\BillingAuditService;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Payment;
use Modules\Billing\Models\Scholarship;
use Modules\Billing\Models\FeeStructure;
use Modules\Billing\Policies\InvoicePolicy;
use Modules\Billing\Policies\PaymentPolicy;
use Modules\Billing\Policies\ScholarshipPolicy;
use Modules\Billing\Policies\FeeStructurePolicy;

class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BillingService::class, function ($app) {
            return new BillingService();
        });

        $this->app->singleton(BillingAuditService::class, function ($app) {
            return new BillingAuditService();
        });
    }

    public function boot(): void
    {
        $this->registerPolicies();
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    private function registerPolicies(): void
    {
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Scholarship::class, ScholarshipPolicy::class);
        Gate::policy(FeeStructure::class, FeeStructurePolicy::class);
    }
}
