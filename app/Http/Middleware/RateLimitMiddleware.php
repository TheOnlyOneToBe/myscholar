<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->getMaxAttempts($request);
        $decayMinutes = $this->getDecayMinutes($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $this->limiter->availableIn($key),
            ], 429)
                ->header('Retry-After', $this->limiter->availableIn($key))
                ->header('X-RateLimit-Limit', $maxAttempts)
                ->header('X-RateLimit-Remaining', 0);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $response
            ->header('X-RateLimit-Limit', $maxAttempts)
            ->header('X-RateLimit-Remaining', max(0, $maxAttempts - $this->limiter->attempts($key)))
            ->header('X-RateLimit-Reset', now()->addMinutes($decayMinutes)->getTimestamp());
    }

    protected function resolveRequestSignature(Request $request): string
    {
        $signature = $request->user()?->id ?? $request->ip();
        $method = $request->method();
        $path = $request->path();

        return "api:{$signature}:{$method}:{$path}";
    }

    protected function getMaxAttempts(Request $request): int
    {
        // Different limits for different endpoint types
        if ($request->path() === 'api/enrollments/export' || $request->path() === 'api/students/export') {
            return 10; // Export endpoints are expensive
        }

        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
            return 60; // Write operations: 60 per minute
        }

        return 120; // Read operations: 120 per minute
    }

    protected function getDecayMinutes(Request $request): int
    {
        return 1; // Reset after 1 minute
    }
}
