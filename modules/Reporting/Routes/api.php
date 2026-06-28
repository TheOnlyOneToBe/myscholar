<?php

use Illuminate\Support\Facades\Route;
use Modules\Reporting\Controllers\ReportController;

Route::middleware(['auth:sanctum'])->prefix('api/reporting')->name('reporting.')->group(function () {

    // Analytics & Dashboard
    Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
    Route::get('/trends', [ReportController::class, 'trendAnalysis'])->name('trends');

    // Student Reports
    Route::prefix('students/{student}')->name('students.')->group(function () {
        Route::get('/academic', [ReportController::class, 'studentAcademicReport'])->name('academic');
        Route::get('/attendance', [ReportController::class, 'studentAttendanceReport'])->name('attendance');
        Route::get('/financial', [ReportController::class, 'studentFinancialReport'])->name('financial');
        Route::get('/progress', [ReportController::class, 'studentProgress'])->name('progress');
    });

    // Class Reports
    Route::get('/classes', [ReportController::class, 'classReport'])->name('classes');

    // School Reports
    Route::get('/school', [ReportController::class, 'schoolSummary'])->name('school');

    // Export
    Route::post('/export', [ReportController::class, 'export'])->name('export');

});
