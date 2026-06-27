<?php

namespace Modules\Audit\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Audit\Services\AuditService;
use Symfony\Component\HttpFoundation\Response;

class AuditHttpMiddleware
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $start) * 1000; // in milliseconds

        // Don't audit health check endpoints or static assets
        $excludedPaths = [
            'health',
            'status',
            '.js',
            '.css',
            '.png',
            '.jpg',
            '.gif',
            '.svg',
        ];

        $shouldAudit = !collect($excludedPaths)->some(
            fn($path) => str_contains($request->path(), $path)
        );

        if ($shouldAudit) {
            $this->auditService->logRequest(
                $request->method(),
                $request->fullUrl(),
                $response->status(),
                $duration,
                [
                    'route' => $request->route()?->getName(),
                    'params' => $request->route()?->parameters(),
                ]
            );
        }

        return $response;
    }
}
