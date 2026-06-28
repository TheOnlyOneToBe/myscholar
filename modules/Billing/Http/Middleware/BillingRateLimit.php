<?php

namespace Modules\Billing\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BillingRateLimit
{
    protected array $limits = [
        'invoice_management' => 50,
        'payment_processing' => 100,
        'scholarship_management' => 30,
        'fee_structure_management' => 20,
        'report_generation' => 10,
        'bulk_operations' => 5,
        'general' => 100,
    ];

    protected array $windows = [
        'invoice_management' => 60,
        'payment_processing' => 60,
        'scholarship_management' => 60,
        'fee_structure_management' => 60,
        'report_generation' => 60,
        'bulk_operations' => 300,
        'general' => 60,
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user) {
            return $next($request);
        }

        $endpoint = $this->identifyEndpoint($request);
        $limit = $this->limits[$endpoint] ?? $this->limits['general'];
        $window = $this->windows[$endpoint] ?? $this->windows['general'];

        $cacheKey = "billing_rate_limit:{$user->id}:{$endpoint}";
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= $limit) {
            return response()->json([
                'message' => 'Rate limit exceeded for this operation',
                'reset_in' => Cache::get("{$cacheKey}:ttl", $window),
            ], 429);
        }

        Cache::put($cacheKey, $attempts + 1, $window);

        return $next($request);
    }

    private function identifyEndpoint(Request $request): string
    {
        $path = $request->path();

        if (str_contains($path, 'invoices')) {
            return 'invoice_management';
        }
        if (str_contains($path, 'payments')) {
            return 'payment_processing';
        }
        if (str_contains($path, 'scholarships')) {
            return 'scholarship_management';
        }
        if (str_contains($path, 'fee-structures')) {
            return 'fee_structure_management';
        }
        if (str_contains($path, 'reports')) {
            return 'report_generation';
        }
        if (str_contains($path, 'bulk')) {
            return 'bulk_operations';
        }

        return 'general';
    }
}
