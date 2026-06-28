<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class ModuleManager
{
    /**
     * Get all installed modules configuration
     */
    public function getInstalledModules(): array
    {
        $modules = [];
        $modulePath = base_path('modules');

        foreach (File::directories($modulePath) as $dir) {
            $moduleName = basename($dir);
            $modulePath = "{$dir}/module.json";

            if (File::exists($modulePath)) {
                $modules[$moduleName] = json_decode(File::get($modulePath), true);
            }
        }

        return $modules;
    }

    /**
     * Check if a module is installed and active
     */
    public function isModuleActive(string $moduleName): bool
    {
        $modules = Cache::remember('modules.active', 3600, function () {
            return $this->getInstalledModules();
        });

        return isset($modules[ucfirst($moduleName)]);
    }

    /**
     * Get module configuration
     */
    public function getModule(string $moduleName): ?array
    {
        $modules = $this->getInstalledModules();
        $key = ucfirst($moduleName);

        return $modules[$key] ?? null;
    }

    /**
     * Check if module has all required dependencies installed
     */
    public function hasDependencies(string $moduleName): bool
    {
        $module = $this->getModule($moduleName);

        if (!$module || empty($module['dependencies'])) {
            return true;
        }

        $installedModules = $this->getInstalledModules();

        foreach ($module['dependencies'] as $dependency) {
            if (!isset($installedModules[ucfirst($dependency)])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get missing dependencies for a module
     */
    public function getMissingDependencies(string $moduleName): array
    {
        $module = $this->getModule($moduleName);
        $missing = [];

        if (!$module || empty($module['dependencies'])) {
            return $missing;
        }

        $installedModules = $this->getInstalledModules();

        foreach ($module['dependencies'] as $dependency) {
            if (!isset($installedModules[ucfirst($dependency)])) {
                $missing[] = $dependency;
            }
        }

        return $missing;
    }

    /**
     * Check if module tables exist in database
     */
    public function moduleTablesExist(string $moduleName): bool
    {
        $module = $this->getModule($moduleName);

        if (!$module || empty($module['tables'])) {
            return true; // Module with no tables is considered as "exists"
        }

        foreach ($module['tables'] as $table) {
            if (!Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get missing tables for a module
     */
    public function getMissingTables(string $moduleName): array
    {
        $module = $this->getModule($moduleName);
        $missing = [];

        if (!$module || empty($module['tables'])) {
            return $missing;
        }

        foreach ($module['tables'] as $table) {
            if (!Schema::hasTable($table)) {
                $missing[] = $table;
            }
        }

        return $missing;
    }

    /**
     * Get full module status
     */
    public function getModuleStatus(string $moduleName): array
    {
        $module = $this->getModule($moduleName);

        if (!$module) {
            return [
                'name' => $moduleName,
                'installed' => false,
                'active' => false,
                'dependencies_met' => false,
                'tables_exist' => false,
                'service_provider' => false,
                'routes_registered' => false,
                'message' => 'Module not found',
            ];
        }

        return [
            'name' => $moduleName,
            'installed' => true,
            'active' => $this->isModuleActive($moduleName),
            'version' => $module['version'] ?? 'unknown',
            'type' => $module['type'] ?? 'unknown',
            'dependencies_met' => $this->hasDependencies($moduleName),
            'missing_dependencies' => $this->getMissingDependencies($moduleName),
            'tables_exist' => $this->moduleTablesExist($moduleName),
            'missing_tables' => $this->getMissingTables($moduleName),
            'service_provider' => $this->serviceProviderExists($moduleName),
            'routes_registered' => $this->routesExist($moduleName),
            'total_tables' => count($module['tables'] ?? []),
            'description' => $module['description'] ?? '',
        ];
    }

    /**
     * Get all modules status
     */
    public function getAllModulesStatus(): array
    {
        $modules = $this->getInstalledModules();
        $status = [];

        foreach (array_keys($modules) as $moduleName) {
            $status[$moduleName] = $this->getModuleStatus($moduleName);
        }

        return $status;
    }

    /**
     * Check if module ServiceProvider exists
     */
    public function serviceProviderExists(string $moduleName): bool
    {
        $modulePath = base_path("modules/{$moduleName}");

        // Check in Providers directory
        if (File::exists("{$modulePath}/Providers/{$moduleName}ServiceProvider.php")) {
            return true;
        }

        // Check in root directory
        if (File::exists("{$modulePath}/{$moduleName}ServiceProvider.php")) {
            return true;
        }

        return false;
    }

    /**
     * Check if module routes exist
     */
    public function routesExist(string $moduleName): bool
    {
        $routePath = base_path("modules/{$moduleName}/Routes");
        return File::exists("{$routePath}/api.php") || File::exists("{$routePath}/web.php");
    }

    /**
     * Verify module can be used for API
     */
    public function canUseModule(string $moduleName): bool
    {
        // Must be installed
        if (!$this->isModuleActive($moduleName)) {
            return false;
        }

        // Must have dependencies
        if (!$this->hasDependencies($moduleName)) {
            return false;
        }

        // Must have tables (if required)
        if (!$this->moduleTablesExist($moduleName)) {
            return false;
        }

        return true;
    }

    /**
     * Get detailed error message if module cannot be used
     */
    public function getModuleError(string $moduleName): ?string
    {
        if (!$this->isModuleActive($moduleName)) {
            return "Module '{$moduleName}' is not installed";
        }

        $missing = $this->getMissingDependencies($moduleName);
        if (!empty($missing)) {
            return "Module '{$moduleName}' is missing dependencies: " . implode(', ', $missing);
        }

        $missingTables = $this->getMissingTables($moduleName);
        if (!empty($missingTables)) {
            return "Module '{$moduleName}' is missing database tables: " . implode(', ', $missingTables);
        }

        return null;
    }

    /**
     * Get module dependency tree
     */
    public function getDependencyTree(string $moduleName, array $visited = []): array
    {
        $module = $this->getModule($moduleName);

        if (!$module) {
            return [];
        }

        $visited[] = $moduleName;
        $dependencies = [];

        foreach ($module['dependencies'] ?? [] as $dep) {
            $depName = ucfirst($dep);
            if (!in_array($depName, $visited)) {
                $dependencies[$depName] = $this->getDependencyTree($depName, $visited);
            }
        }

        return [
            'module' => $moduleName,
            'version' => $module['version'] ?? '1.0.0',
            'dependencies' => $dependencies,
        ];
    }

    /**
     * Clear module cache
     */
    public function clearCache(): void
    {
        Cache::forget('modules.active');
    }
}
