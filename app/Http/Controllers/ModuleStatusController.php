<?php

namespace App\Http\Controllers;

use App\Services\ModuleManager;
use Illuminate\Http\JsonResponse;

class ModuleStatusController extends Controller
{
    public function __construct(
        private ModuleManager $moduleManager
    ) {}

    /**
     * Get all installed modules status
     */
    public function index(): JsonResponse
    {
        $modules = $this->moduleManager->getAllModulesStatus();

        return response()->json([
            'data' => $modules,
            'total_modules' => count($modules),
            'active_modules' => collect($modules)->filter(fn($m) => $m['active'])->count(),
            'fully_operational' => collect($modules)
                ->filter(fn($m) => $m['active'] && $m['dependencies_met'] && $m['tables_exist'])
                ->count(),
        ]);
    }

    /**
     * Get specific module status
     */
    public function show(string $moduleName): JsonResponse
    {
        $status = $this->moduleManager->getModuleStatus($moduleName);

        if (!isset($status['installed']) || !$status['installed']) {
            return response()->json([
                'error' => 'Module not found',
                'message' => "Module '{$moduleName}' is not installed",
            ], 404);
        }

        return response()->json([
            'data' => $status,
        ]);
    }

    /**
     * Get module dependency tree
     */
    public function dependencies(string $moduleName): JsonResponse
    {
        $tree = $this->moduleManager->getDependencyTree($moduleName);

        if (empty($tree)) {
            return response()->json([
                'error' => 'Module not found',
                'message' => "Module '{$moduleName}' is not installed",
            ], 404);
        }

        return response()->json([
            'data' => $tree,
        ]);
    }

    /**
     * Verify module can be used
     */
    public function verify(string $moduleName): JsonResponse
    {
        $canUse = $this->moduleManager->canUseModule($moduleName);
        $error = $this->moduleManager->getModuleError($moduleName);
        $status = $this->moduleManager->getModuleStatus($moduleName);

        return response()->json([
            'module' => $moduleName,
            'can_use' => $canUse,
            'error' => $error,
            'status' => $status,
        ], $canUse ? 200 : 503);
    }

    /**
     * Get system health check
     */
    public function health(): JsonResponse
    {
        $allModules = $this->moduleManager->getAllModulesStatus();

        $healthStatus = [
            'healthy' => true,
            'modules_installed' => collect($allModules)->filter(fn($m) => $m['installed'])->count(),
            'modules_active' => collect($allModules)->filter(fn($m) => $m['active'])->count(),
            'modules_fully_operational' => collect($allModules)
                ->filter(fn($m) => $m['active'] && $m['dependencies_met'] && $m['tables_exist'])
                ->count(),
            'issues' => [],
        ];

        foreach ($allModules as $moduleName => $status) {
            if ($status['active']) {
                if (!$status['dependencies_met']) {
                    $healthStatus['healthy'] = false;
                    $healthStatus['issues'][] = [
                        'module' => $moduleName,
                        'type' => 'missing_dependencies',
                        'missing' => $status['missing_dependencies'],
                    ];
                }

                if (!$status['tables_exist']) {
                    $healthStatus['healthy'] = false;
                    $healthStatus['issues'][] = [
                        'module' => $moduleName,
                        'type' => 'missing_tables',
                        'missing' => $status['missing_tables'],
                    ];
                }
            }
        }

        return response()->json([
            'data' => $healthStatus,
        ], $healthStatus['healthy'] ? 200 : 503);
    }
}
