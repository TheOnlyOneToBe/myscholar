<?php

namespace Modules\Config\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Config\Services\SchoolYearSessionService;

/**
 * Initialize School Year Session Middleware
 * Ensures school year is always available in session for data filtering
 */
class InitializeSchoolYearSession
{
    public function handle(Request $request, Closure $next)
    {
        // Only initialize if user is authenticated
        if (auth()->check()) {
            try {
                $service = app(SchoolYearSessionService::class);
                $service->initializeSession();
            } catch (\RuntimeException $e) {
                // If no school years exist, continue without setting
                // This allows installation to proceed
            }
        }

        return $next($request);
    }
}
