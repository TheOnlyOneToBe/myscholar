<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Controllers\AuthController;
use Modules\Auth\Controllers\UserController;
use Modules\Auth\Controllers\RoleController;
use Modules\Auth\Controllers\PermissionController;

Route::prefix('api/auth')->group(function () {
    // Public routes (no authentication required)
    // Rate limited: 5 attempts per minute per IP for login
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Rate limited: 3 attempts per hour per IP for password reset
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,60');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');
    Route::post('/validate-token', [AuthController::class, 'validateResetToken']);

    // Protected routes (authentication required)
    Route::middleware(['auth:sanctum'])->group(function () {
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);

        // User management routes
        Route::apiResource('users', UserController::class);
        Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole']);
        Route::post('/users/{user}/remove-role', [UserController::class, 'removeRole']);
        Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate']);
        Route::post('/users/{user}/activate', [UserController::class, 'activate']);

        // Role management routes
        Route::get('/roles', [RoleController::class, 'index']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions']);
        Route::post('/roles/{role}/give-permissions', [RoleController::class, 'givePermissions']);
        Route::post('/roles/{role}/revoke-permissions', [RoleController::class, 'revokePermissions']);

        // Permission routes
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
        Route::get('/permissions/by-module', [PermissionController::class, 'byModule']);
        Route::get('/me/permissions', [PermissionController::class, 'userPermissions']);
        Route::post('/me/check-permission', [PermissionController::class, 'checkPermission']);
    });
});
