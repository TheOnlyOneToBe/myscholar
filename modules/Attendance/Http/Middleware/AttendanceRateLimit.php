<?php

namespace Modules\Attendance\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Modules\Attendance\Services\IPBlockingService;
use Symfony\Component\HttpFoundation\Response;

class AttendanceRateLimit
{
    protected RateLimiter $limiter;
    protected IPBlockingService $ipBlockingService;

    public function __construct(RateLimiter $limiter, IPBlockingService $ipBlockingService)
    {
        $this->limiter = $limiter;
        $this->ipBlockingService = $ipBlockingService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        $limits = $this->getLimits($request);
        $ipAddress = $request->ip();

        if ($this->limiter->tooManyAttempts($key, $limits['max_attempts'])) {
            $this->ipBlockingService->trackRateLimitViolation($ipAddress, $limits['type']);

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

        return "attendance:{$userId}:{$method}:{$endpoint}";
    }

    protected function getEndpointType(string $path): string
    {
        if (str_contains($path, '/bulk')) {
            return 'bulk';
        }
        if (str_contains($path, '/records')) {
            return 'records';
        }
        if (str_contains($path, '/sessions')) {
            return 'sessions';
        }
        if (str_contains($path, '/justifications')) {
            return 'justifications';
        }
        if (str_contains($path, '/absences')) {
            return 'absences';
        }

        return 'general';
    }

    protected function getLimits(Request $request): array
    {
        $path = $request->path();
        $method = $request->method();

        // Bulk operations - more restrictive
        if (str_contains($path, '/bulk')) {
            return [
                'type' => 'bulk_operation',
                'max_attempts' => 10,           // 10 bulk operations per minute
                'decay_minutes' => 1,
            ];
        }

        // POST/PUT records (marking attendance) - moderate limit
        if ((str_contains($path, '/records') || str_contains($path, '/mark')) && in_array($method, ['POST', 'PUT'])) {
            return [
                'type' => 'attendance_marking',
                'max_attempts' => 120,          // 120 marks per minute (2/second)
                'decay_minutes' => 1,
            ];
        }

        // GET requests (reading data) - generous limit
        if ($method === 'GET') {
            return [
                'type' => 'read_operation',
                'max_attempts' => 300,          // 300 reads per minute
                'decay_minutes' => 1,
            ];
        }

        // DELETE operations - very restrictive
        if ($method === 'DELETE') {
            return [
                'type' => 'delete_operation',
                'max_attempts' => 20,           // 20 deletes per minute
                'decay_minutes' => 1,
            ];
        }

        // Justification approval - moderate
        if (str_contains($path, '/justifications') && in_array($method, ['PATCH', 'PUT'])) {
            return [
                'type' => 'justification_review',
                'max_attempts' => 60,           // 60 reviews per minute
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
