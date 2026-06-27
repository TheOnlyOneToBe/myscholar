<?php

use Illuminate\Support\Facades\Route;
use Modules\Config\Controllers\SchoolInfoController;

Route::prefix('api/config')->group(function () {
    Route::get('/school', [SchoolInfoController::class, 'show']);
    Route::put('/school', [SchoolInfoController::class, 'update']);
    Route::post('/school/logo', [SchoolInfoController::class, 'uploadLogo']);

    Route::get('/settings', [SchoolInfoController::class, 'settings']);
    Route::put('/settings', [SchoolInfoController::class, 'updateSettings']);
});
