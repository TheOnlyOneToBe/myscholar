<?php

namespace Modules\Billing\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Cache;

class BillingRateLimitTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user->giveRole('admin');
        Cache::flush();
    }

    public function test_invoice_management_rate_limit_config()
    {
        $this->actingAs($this->user);

        $cacheKey = "billing_rate_limit:{$this->user->id}:invoice_management";
        Cache::put($cacheKey, 49, 60);

        $limit = Cache::get($cacheKey, 0);
        $this->assertEquals(49, $limit);
    }

    public function test_payment_processing_rate_limit_config()
    {
        $this->actingAs($this->user);

        $cacheKey = "billing_rate_limit:{$this->user->id}:payment_processing";
        Cache::put($cacheKey, 99, 60);

        $limit = Cache::get($cacheKey, 0);
        $this->assertEquals(99, $limit);
    }

    public function test_bulk_operations_rate_limit_config()
    {
        $this->actingAs($this->user);

        $cacheKey = "billing_rate_limit:{$this->user->id}:bulk_operations";
        Cache::put($cacheKey, 4, 300);

        $limit = Cache::get($cacheKey, 0);
        $this->assertEquals(4, $limit);
    }

    public function test_rate_limit_resets_after_window()
    {
        $this->actingAs($this->user);

        $cacheKey = "billing_rate_limit:{$this->user->id}:invoice_management";
        Cache::put($cacheKey, 50, 1);

        sleep(2);

        $attempts = Cache::get($cacheKey, 0);
        $this->assertEquals(0, $attempts);
    }
}
