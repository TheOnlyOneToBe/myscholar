<?php

namespace App\Traits;

use App\Services\ModuleManager;
use Illuminate\Http\JsonResponse;

trait VerifiesModuleAccess
{
    /**
     * Verify module is active and accessible
     */
    protected function verifyModuleAccess(string $moduleName): ?JsonResponse
    {
        $moduleManager = app(ModuleManager::class);

        if (!$moduleManager->isModuleActive($moduleName)) {
            return response()->json([
                'error' => 'Module not installed',
                'message' => "Module '{$moduleName}' is not installed",
                'module' => $moduleName,
            ], 503);
        }

        if (!$moduleManager->hasDependencies($moduleName)) {
            $missing = $moduleManager->getMissingDependencies($moduleName);
            return response()->json([
                'error' => 'Missing dependencies',
                'message' => "Module '{$moduleName}' requires: " . implode(', ', $missing),
                'missing_dependencies' => $missing,
                'module' => $moduleName,
            ], 503);
        }

        if (!$moduleManager->moduleTablesExist($moduleName)) {
            $missing = $moduleManager->getMissingTables($moduleName);
            return response()->json([
                'error' => 'Database tables not found',
                'message' => "Module '{$moduleName}' is missing tables: " . implode(', ', $missing),
                'missing_tables' => $missing,
                'module' => $moduleName,
            ], 503);
        }

        return null;
    }

    /**
     * Verify table exists before querying
     */
    protected function verifyTableExists(string $tableName): ?JsonResponse
    {
        if (!\Schema::hasTable($tableName)) {
            return response()->json([
                'error' => 'Table not found',
                'message' => "Database table '{$tableName}' does not exist",
                'table' => $tableName,
            ], 503);
        }

        return null;
    }

    /**
     * Verify multiple tables exist
     */
    protected function verifyTablesExist(array $tables): ?JsonResponse
    {
        $missing = [];

        foreach ($tables as $table) {
            if (!\Schema::hasTable($table)) {
                $missing[] = $table;
            }
        }

        if (!empty($missing)) {
            return response()->json([
                'error' => 'Database tables not found',
                'message' => 'Missing database tables: ' . implode(', ', $missing),
                'missing_tables' => $missing,
            ], 503);
        }

        return null;
    }

    /**
     * Verify column exists in table
     */
    protected function verifyColumnExists(string $table, string $column): ?JsonResponse
    {
        if (!\Schema::hasTable($table)) {
            return response()->json([
                'error' => 'Table not found',
                'message' => "Database table '{$table}' does not exist",
                'table' => $table,
            ], 503);
        }

        if (!\Schema::hasColumn($table, $column)) {
            return response()->json([
                'error' => 'Column not found',
                'message' => "Database column '{$table}.{$column}' does not exist",
                'table' => $table,
                'column' => $column,
            ], 503);
        }

        return null;
    }

    /**
     * Get module status information
     */
    protected function getModuleStatus(string $moduleName): array
    {
        $moduleManager = app(ModuleManager::class);
        return $moduleManager->getModuleStatus($moduleName);
    }

    /**
     * Check if bridge link exists (foreign key column)
     */
    protected function verifyBridgeLink(string $table, string $foreignKeyColumn): ?JsonResponse
    {
        $verification = $this->verifyColumnExists($table, $foreignKeyColumn);

        if ($verification) {
            return response()->json([
                'error' => 'Module bridge not installed',
                'message' => "Bridge linking is not properly installed for '{$table}' module",
                'table' => $table,
                'missing_bridge_column' => $foreignKeyColumn,
            ], 503);
        }

        return null;
    }
}
