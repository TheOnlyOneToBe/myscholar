<?php

use Illuminate\Support\Facades\Route;
use Modules\Notifications\Controllers\NotificationController;
use Modules\Notifications\Controllers\NotificationActionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::get('/pending', [NotificationController::class, 'pending']);
        Route::get('/{notification}', [NotificationController::class, 'show']);
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/{notification}/mark-as-unread', [NotificationController::class, 'markAsUnread']);
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'delete']);

        Route::post('/{notification}/approve', [NotificationActionController::class, 'approve']);
        Route::post('/{notification}/reject', [NotificationActionController::class, 'reject']);
    });
});
