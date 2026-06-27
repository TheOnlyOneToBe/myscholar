<?php

use Illuminate\Support\Facades\Route;
use Modules\Audit\Controllers\AuditLogController;

Route::prefix('api/audit')->middleware('auth:sanctum')->group(function () {
    // Audit logs CRUD
    Route::get('/logs', [AuditLogController::class, 'index']);
    Route::get('/logs/{log}', [AuditLogController::class, 'show']);

    // Monitoring & Dashboard
    Route::get('/recent-errors', [AuditLogController::class, 'recentErrors']);
    Route::get('/crashes', [AuditLogController::class, 'crashes']);
    Route::get('/failed-requests', [AuditLogController::class, 'failedRequests']);
    Route::get('/stats', [AuditLogController::class, 'stats']);

    // User activity
    Route::get('/user-activity', [AuditLogController::class, 'userActivity']);

    // Export
    Route::get('/export', [AuditLogController::class, 'export']);
});
