<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Controllers\AttendanceSessionController;
use Modules\Attendance\Controllers\AttendanceController;
use Modules\Attendance\Controllers\JustificationController;
use Modules\Attendance\Controllers\AbsenceController;

Route::prefix('api/attendance')->middleware(['api', 'auth'])->group(function () {
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
});
