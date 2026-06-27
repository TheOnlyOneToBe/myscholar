<?php

namespace App\Services;

use Exception;

class DatabaseConfigManager
{
    protected array $config = [];
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = base_path('storage/database-config.json');
        $this->load();
    }

    /**
     * Configure MySQL connection
     */
    public function configureMysql(
        string $host,
        string $database,
        string $username,
        string $password = '',
        int $port = 3306,
        string $charset = 'utf8mb4',
        string $collation = 'utf8mb4_unicode_ci'
    ): self {
        $this->config = [
            'driver' => 'mysql',
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => $charset,
            'collation' => $collation,
        ];

        return $this;
    }

    /**
     * Configure SQLite connection
     */
    public function configureSqlite(?string $databasePath = null): self
    {
        $this->config = [
            'driver' => 'sqlite',
            'database' => $databasePath ?? database_path('database.sqlite'),
        ];

        return $this;
    }

    /**
     * Get current configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get specific config value
     */
    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Check if database is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->config) && isset($this->config['driver']);
    }

    /**
     * Get driver type
     */
    public function getDriver(): ?string
    {
        return $this->config['driver'] ?? null;
    }

    /**
     * Check if using MySQL
     */
    public function isMysql(): bool
    {
        return ($this->config['driver'] ?? null) === 'mysql';
    }

    /**
     * Check if using SQLite
     */
    public function isSqlite(): bool
    {
        return ($this->config['driver'] ?? null) === 'sqlite';
    }

    /**
     * Save configuration to file
     */
    public function save(): bool
    {
        if (!$this->isConfigured()) {
            throw new Exception('Database configuration not set');
        }

        $dir = dirname($this->configPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $json = json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return file_put_contents($this->configPath, $json) !== false;
    }

    /**
     * Load configuration from file
     */
    public function load(): bool
    {
        if (!file_exists($this->configPath)) {
            return false;
        }

        $json = file_get_contents($this->configPath);
        $config = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $this->config = $config ?? [];
        return true;
    }

    /**
     * Clear configuration
     */
    public function clear(): self
    {
        $this->config = [];
        if (file_exists($this->configPath)) {
            unlink($this->configPath);
        }

        return $this;
    }

    /**
     * Export as Laravel database config format
     */
    public function toLaravelConfig(): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        if ($this->isMysql()) {
            return [
                'driver' => 'mysql',
                'url' => null,
                'host' => $this->config['host'],
                'port' => $this->config['port'],
                'database' => $this->config['database'],
                'username' => $this->config['username'],
                'password' => $this->config['password'],
                'unix_socket' => '',
                'charset' => $this->config['charset'] ?? 'utf8mb4',
                'collation' => $this->config['collation'] ?? 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
            ];
        }

        if ($this->isSqlite()) {
            return [
                'driver' => 'sqlite',
                'url' => null,
                'database' => $this->config['database'],
                'prefix' => '',
                'foreign_key_constraints' => true,
            ];
        }

        return [];
    }

    /**
     * Get connection string (for display/debug)
     */
    public function getConnectionString(): string
    {
        if ($this->isMysql()) {
            return sprintf(
                'mysql://%s@%s:%d/%s',
                $this->config['username'] ?? 'root',
                $this->config['host'] ?? 'localhost',
                $this->config['port'] ?? 3306,
                $this->config['database'] ?? 'unknown'
            );
        }

        if ($this->isSqlite()) {
            return 'sqlite:///' . ($this->config['database'] ?? 'unknown');
        }

        return 'not configured';
    }

    /**
     * Get config path (for debugging)
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }
}
