<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModuleStatusController;

Route::get('/ping', fn () => response()->json(['status' => 'ok']));

// System/Module Status Endpoints
Route::prefix('system')->name('system.')->group(function () {
    Route::prefix('modules')->name('modules.')->group(function () {
        Route::get('/', [ModuleStatusController::class, 'index'])->name('index');
        Route::get('/health', [ModuleStatusController::class, 'health'])->name('health');
        Route::get('/{module}/status', [ModuleStatusController::class, 'show'])->name('show');
        Route::get('/{module}/dependencies', [ModuleStatusController::class, 'dependencies'])->name('dependencies');
        Route::get('/{module}/verify', [ModuleStatusController::class, 'verify'])->name('verify');
    });
});

// Load module routes
require_once base_path('modules/Auth/Routes/api.php');
