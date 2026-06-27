<?php

namespace App\Http\Controllers;

use App\Services\DatabaseConfigManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatabaseConfigController extends Controller
{
    protected DatabaseConfigManager $configManager;

    public function __construct(DatabaseConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * Get current database configuration
     */
    public function show(): JsonResponse
    {
        if (!$this->configManager->isConfigured()) {
            return response()->json([
                'configured' => false,
                'message' => 'Database not configured'
            ], 200);
        }

        $config = $this->configManager->getConfig();

        // Don't expose password in response
        if ($config['driver'] === 'mysql') {
            unset($config['password']);
        }

        return response()->json([
            'configured' => true,
            'driver' => $config['driver'],
            'connection_string' => $this->configManager->getConnectionString(),
            'config' => $config,
        ]);
    }

    /**
     * Configure SQLite database
     */
    public function setupSqlite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'database' => 'nullable|string',
        ]);

        try {
            $this->configManager->configureSqlite($validated['database'] ?? null);
            $this->configManager->save();

            return response()->json([
                'success' => true,
                'message' => 'SQLite configured successfully',
                'driver' => 'sqlite',
                'connection_string' => $this->configManager->getConnectionString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to configure SQLite: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Configure MySQL database
     */
    public function setupMysql(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'database' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
            'charset' => 'nullable|string',
            'collation' => 'nullable|string',
        ]);

        try {
            $this->configManager->configureMysql(
                host: $validated['host'],
                database: $validated['database'],
                username: $validated['username'],
                password: $validated['password'] ?? '',
                port: $validated['port'],
                charset: $validated['charset'] ?? 'utf8mb4',
                collation: $validated['collation'] ?? 'utf8mb4_unicode_ci'
            );
            $this->configManager->save();

            return response()->json([
                'success' => true,
                'message' => 'MySQL configured successfully',
                'driver' => 'mysql',
                'connection_string' => $this->configManager->getConnectionString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to configure MySQL: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Clear database configuration
     */
    public function clear(): JsonResponse
    {
        try {
            $this->configManager->clear();

            return response()->json([
                'success' => true,
                'message' => 'Database configuration cleared',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear configuration: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get configuration file path (for debugging)
     */
    public function getConfigPath(): JsonResponse
    {
        return response()->json([
            'config_path' => $this->configManager->getConfigPath(),
            'config_exists' => file_exists($this->configManager->getConfigPath()),
        ]);
    }
}
