<?php

use App\Services\AlertService;
use App\Services\DatabaseConfigManager;

/**
 * Get the alert service instance
 */
function alerts(): AlertService
{
    return app(AlertService::class);
}

/**
 * Add a success alert
 */
function alert_success(string $message, ?string $code = null): AlertService
{
    return alerts()->success($message, $code);
}

/**
 * Add a warning alert
 */
function alert_warning(string $message, ?string $code = null): AlertService
{
    return alerts()->warning($message, $code);
}

/**
 * Add an error alert
 */
function alert_error(string $message, ?string $code = null): AlertService
{
    return alerts()->error($message, $code);
}

/**
 * Get all alerts
 */
function get_alerts(): array
{
    return alerts()->all();
}

/**
 * Clear all alerts
 */
function clear_alerts(): void
{
    alerts()->clear();
}

/**
 * Get the database config manager instance
 */
function database_config(): DatabaseConfigManager
{
    return app(DatabaseConfigManager::class);
}

/**
 * Check if database is configured
 */
function is_database_configured(): bool
{
    return database_config()->isConfigured();
}

/**
 * Get database driver (mysql or sqlite)
 */
function get_database_driver(): ?string
{
    return database_config()->getDriver();
}

/**
 * Check if using MySQL
 */
function is_mysql(): bool
{
    return database_config()->isMysql();
}

/**
 * Check if using SQLite
 */
function is_sqlite(): bool
{
    return database_config()->isSqlite();
}
