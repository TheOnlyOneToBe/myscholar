<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ModuleManager;

class VerifyModuleActivation
{
    public function __construct(
        private ModuleManager $moduleManager
    ) {}

    public function handle(Request $request, Closure $next)
    {
        // Extract module name from route
        $moduleName = $this->extractModuleName($request);

        if ($moduleName) {
            // Check if module can be used
            if (!$this->moduleManager->canUseModule($moduleName)) {
                $error = $this->moduleManager->getModuleError($moduleName);

                return response()->json([
                    'error' => 'Module not available',
                    'message' => $error ?? "Module '{$moduleName}' is not properly installed",
                    'module' => $moduleName,
                ], 503);
            }

            // Store module info in request for later use
            $request->moduleInfo = $this->moduleManager->getModuleStatus($moduleName);
        }

        return $next($request);
    }

    /**
     * Extract module name from request path
     * Expected format: /api/{module}/...
     */
    private function extractModuleName(Request $request): ?string
    {
        $path = $request->getPathInfo();
        $segments = explode('/', trim($path, '/'));

        // Expected: ['api', 'module_name', ...]
        if (count($segments) >= 2 && $segments[0] === 'api') {
            $moduleName = $segments[1];
            // Normalize module name (e.g., 'students' -> 'Students')
            return ucfirst($moduleName);
        }

        return null;
    }
}
