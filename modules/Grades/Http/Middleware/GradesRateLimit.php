<?php

namespace Modules\Grades\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class GradesRateLimit
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        $limits = $this->getLimits($request);

        if ($this->limiter->tooManyAttempts($key, $limits['max_attempts'])) {
            return response()->json([
                'message' => 'Rate limit exceeded for this operation',
                'retry_after' => $this->limiter->availableIn($key),
                'limit_type' => $limits['type'],
            ], 429)
                ->header('Retry-After', $this->limiter->availableIn($key))
                ->header('X-RateLimit-Limit', $limits['max_attempts'])
                ->header('X-RateLimit-Remaining', 0);
        }

        $this->limiter->hit($key, $limits['decay_minutes'] * 60);

        $response = $next($request);

        return $response
            ->header('X-RateLimit-Limit', $limits['max_attempts'])
            ->header('X-RateLimit-Remaining', max(0, $limits['max_attempts'] - $this->limiter->attempts($key)))
            ->header('X-RateLimit-Reset', now()->addMinutes($limits['decay_minutes'])->getTimestamp())
            ->header('X-RateLimit-Type', $limits['type']);
    }

    protected function resolveRequestSignature(Request $request): string
    {
        $userId = $request->user()?->id ?? $request->ip();
        $method = $request->method();
        $endpoint = $this->getEndpointType($request->path());

        return "grades:{$userId}:{$method}:{$endpoint}";
    }

    protected function getEndpointType(string $path): string
    {
        if (str_contains($path, '/subjects')) {
            return 'subjects';
        }
        if (str_contains($path, '/grade-appeals')) {
            return 'appeals';
        }
        if (str_contains($path, '/grades')) {
            return 'grades';
        }

        return 'general';
    }

    protected function getLimits(Request $request): array
    {
        $path = $request->path();
        $method = $request->method();

        // Subject management - restrictive (admin only)
        if (str_contains($path, '/subjects') && in_array($method, ['POST', 'PUT', 'DELETE'])) {
            return [
                'type' => 'subject_management',
                'max_attempts' => 20,  // 20 per minute (admin only)
                'decay_minutes' => 1,
            ];
        }

        // Grade creation/update (teachers bulk entering grades)
        if ((str_contains($path, '/grades') || str_contains($path, '/grades/')) && in_array($method, ['POST', 'PUT'])) {
            return [
                'type' => 'grade_entry',
                'max_attempts' => 100,  // 100 grades per minute (faster than attendance)
                'decay_minutes' => 1,
            ];
        }

        // Grade appeals - moderate limit
        if (str_contains($path, '/grade-appeals') && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            return [
                'type' => 'grade_appeal',
                'max_attempts' => 10,  // 10 appeals per minute
                'decay_minutes' => 1,
            ];
        }

        // GET requests (reading grades) - generous
        if ($method === 'GET') {
            return [
                'type' => 'read_operation',
                'max_attempts' => 300,  // 300 reads per minute
                'decay_minutes' => 1,
            ];
        }

        // DELETE operations - very restrictive
        if ($method === 'DELETE') {
            return [
                'type' => 'delete_operation',
                'max_attempts' => 10,  // 10 deletes per minute
                'decay_minutes' => 1,
            ];
        }

        // Default
        return [
            'type' => 'general',
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ];
    }
}
