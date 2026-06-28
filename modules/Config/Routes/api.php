<?php

use Illuminate\Support\Facades\Route;
use Modules\Config\Controllers\SchoolInfoController;
use Modules\Config\Controllers\SystemSettingController;
use Modules\Config\Controllers\SchoolYearController;
use Modules\Config\Controllers\AcademicPeriodController;

Route::prefix('api/config')->middleware('auth')->group(function () {
    // School Info Management
    Route::middleware('can:config.view')->group(function () {
        Route::get('/school', [SchoolInfoController::class, 'show']);
        Route::get('/settings', [SchoolInfoController::class, 'settings']);
    });

    Route::middleware('can:config.school_info.edit')->group(function () {
        Route::put('/school', [SchoolInfoController::class, 'update']);
    });

    Route::middleware('can:config.school_info.logo')->group(function () {
        Route::post('/school/logo', [SchoolInfoController::class, 'uploadLogo']);
    });

    Route::middleware('can:config.settings.edit')->group(function () {
        Route::put('/settings', [SchoolInfoController::class, 'updateSettings']);
    });

    // System Settings CRUD
    Route::middleware('can:config.settings.view')->group(function () {
        Route::get('/system-settings', [SystemSettingController::class, 'index']);
        Route::get('/system-settings/group/{group}', [SystemSettingController::class, 'getByGroup']);
        Route::get('/system-settings/{setting}', [SystemSettingController::class, 'getSetting']);
    });

    Route::middleware('can:config.settings.edit')->group(function () {
        Route::post('/system-settings', [SystemSettingController::class, 'store']);
        Route::put('/system-settings/{setting}', [SystemSettingController::class, 'update']);
        Route::delete('/system-settings/{setting}', [SystemSettingController::class, 'destroy']);
        Route::put('/system-settings/bulk/update', [SystemSettingController::class, 'bulkUpdate']);
    });

    // School Year Management
    Route::middleware('can:config.school_year.view')->group(function () {
        Route::get('/school-years', [SchoolYearController::class, 'index']);
        Route::get('/school-years/current', [SchoolYearController::class, 'current']);
        Route::get('/school-years/list', [SchoolYearController::class, 'list']);
        Route::get('/school-years/{schoolYear}', [SchoolYearController::class, 'show']);
    });

    Route::middleware('can:config.school_year.create')->group(function () {
        Route::post('/school-years', [SchoolYearController::class, 'store']);
    });

    Route::middleware('can:config.school_year.edit')->group(function () {
        Route::put('/school-years/{schoolYear}', [SchoolYearController::class, 'update']);
        Route::post('/school-years/{schoolYear}/activate', [SchoolYearController::class, 'activate']);
    });

    Route::middleware('can:config.school_year.delete')->group(function () {
        Route::delete('/school-years/{schoolYear}', [SchoolYearController::class, 'destroy']);
    });

    // Academic Periods Management
    Route::middleware('can:config.view')->group(function () {
        Route::get('/academic-periods', [AcademicPeriodController::class, 'index']);
        Route::get('/academic-periods/current', [AcademicPeriodController::class, 'current']);
        Route::get('/academic-periods/{termNumber}', [AcademicPeriodController::class, 'show']);
    });

    Route::middleware('can:config.edit')->group(function () {
        Route::put('/academic-periods/{id}', [AcademicPeriodController::class, 'update']);
        Route::post('/academic-periods/initialize', [AcademicPeriodController::class, 'initialize']);
    });
});
