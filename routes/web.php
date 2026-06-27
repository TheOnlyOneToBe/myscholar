<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseConfigController;

Route::get('/', function () {
    return view('welcome');
});

// Database Configuration Routes
Route::prefix('api/config')->group(function () {
    Route::get('/database', [DatabaseConfigController::class, 'show']);
    Route::post('/database/sqlite', [DatabaseConfigController::class, 'setupSqlite']);
    Route::post('/database/mysql', [DatabaseConfigController::class, 'setupMysql']);
    Route::delete('/database', [DatabaseConfigController::class, 'clear']);
    Route::get('/database/path', [DatabaseConfigController::class, 'getConfigPath']);
});

// Test Alert System Routes
require __DIR__ . '/test-alerts.php';
