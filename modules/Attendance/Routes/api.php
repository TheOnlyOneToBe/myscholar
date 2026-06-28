<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Controllers\AttendanceSessionController;
use Modules\Attendance\Controllers\AttendanceController;
use Modules\Attendance\Controllers\JustificationController;
use Modules\Attendance\Controllers\AbsenceController;
use Modules\Attendance\Controllers\BulkAttendanceController;
use Modules\Attendance\Controllers\IPBlockingController;

Route::prefix('api/attendance')
    ->middleware(['api', 'auth', \Modules\Attendance\Http\Middleware\CheckIPBlocklist::class, \Modules\Attendance\Http\Middleware\AttendanceRateLimit::class])
    ->group(function () {
    // Attendance Sessions
    Route::resource('sessions', AttendanceSessionController::class);
    Route::get('sessions/class/{classId}', [AttendanceSessionController::class, 'byClass']);
    Route::get('sessions/subject/{subjectId}', [AttendanceSessionController::class, 'bySubject']);
    Route::get('sessions/{sessionId}/report', [AttendanceSessionController::class, 'report']);

    // Attendance Records
    Route::resource('records', AttendanceController::class);
    Route::get('records/student/{studentId}', [AttendanceController::class, 'byStudent']);
    Route::get('records/session/{sessionId}', [AttendanceController::class, 'bySession']);
    Route::get('student/{studentId}/attendance-rate', [AttendanceController::class, 'studentAttendanceRate']);
    Route::get('class/{classId}/overview', [AttendanceController::class, 'classOverview']);

    // Justifications
    Route::resource('justifications', JustificationController::class);
    Route::get('justifications/student/{studentId}', [JustificationController::class, 'byStudent']);
    Route::get('justifications/pending', [JustificationController::class, 'pending']);
    Route::patch('justifications/{justification}/approve', [JustificationController::class, 'approve']);
    Route::patch('justifications/{justification}/reject', [JustificationController::class, 'reject']);

    // Absence Counters and Alerts
    Route::get('absences/student/{studentId}/counter', [AbsenceController::class, 'getCounter']);
    Route::get('absences/student/{studentId}/alerts', [AbsenceController::class, 'getAlerts']);
    Route::get('absences/pending-alerts', [AbsenceController::class, 'getPendingAlerts']);
    Route::patch('absences/alerts/{alert}/acknowledge', [AbsenceController::class, 'acknowledge']);
    Route::post('absences/check-thresholds/{studentId}', [AbsenceController::class, 'checkThresholds']);
    Route::get('absences/student/{studentId}/stats', [AbsenceController::class, 'getStats']);

    // Bulk Operations
    Route::post('bulk/mark', [BulkAttendanceController::class, 'markBulk']);
    Route::post('bulk/validate', [BulkAttendanceController::class, 'validateBulk']);
    Route::get('bulk/template', [BulkAttendanceController::class, 'getTemplate']);
    Route::get('bulk/summary/{sessionId}', [BulkAttendanceController::class, 'getSummary']);
    Route::post('bulk/import', [BulkAttendanceController::class, 'importBulk']);

    // IP Blocking Management (admin only)
    Route::prefix('ip-blocking')->group(function () {
        Route::get('active-blocks', [IPBlockingController::class, 'getActiveBlocks']);
        Route::post('block', [IPBlockingController::class, 'blockIP']);
        Route::post('unblock', [IPBlockingController::class, 'unblockIP']);
        Route::get('info/{ipAddress}', [IPBlockingController::class, 'getBlockInfo']);
        Route::get('violations/{ipAddress}', [IPBlockingController::class, 'getViolationHistory']);
        Route::post('cleanup', [IPBlockingController::class, 'cleanupExpiredBlocks']);
    });
});
