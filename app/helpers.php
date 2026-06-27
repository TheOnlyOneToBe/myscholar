<?php

use App\Services\AlertService;

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
