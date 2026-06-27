<?php

use Illuminate\Support\Facades\Route;
use Modules\Config\Controllers\SchoolInfoController;
use Modules\Config\Controllers\SchoolYearSessionController;

Route::prefix('api/config')->group(function () {
    Route::get('/school', [SchoolInfoController::class, 'show']);
    Route::put('/school', [SchoolInfoController::class, 'update']);
    Route::post('/school/logo', [SchoolInfoController::class, 'uploadLogo']);

    Route::get('/settings', [SchoolInfoController::class, 'settings']);
    Route::put('/settings', [SchoolInfoController::class, 'updateSettings']);

    // School Year Session Management
    Route::prefix('school-years')->group(function () {
        Route::get('/current', [SchoolYearSessionController::class, 'current']);
        Route::get('/', [SchoolYearSessionController::class, 'index']);
        Route::post('/switch', [SchoolYearSessionController::class, 'switch']);
        Route::get('/{schoolYear}', [SchoolYearSessionController::class, 'info']);
    });
});
