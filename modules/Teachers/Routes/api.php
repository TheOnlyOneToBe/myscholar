<?php

use Illuminate\Support\Facades\Route;
use Modules\Teachers\Controllers\TeacherController;
use Modules\Teachers\Controllers\TeacherAssignmentController;
use Modules\Teachers\Controllers\TeacherApplicationController;

Route::middleware('auth:api')->prefix('teachers')->group(function () {
    // Teachers CRUD
    Route::apiResource('', TeacherController::class);

    // Teacher-specific endpoints
    Route::get('{teacher}/qualifications', [TeacherController::class, 'getQualifications']);
    Route::get('{teacher}/classes', [TeacherController::class, 'getClasses']);
    Route::get('{teacher}/hours', [TeacherController::class, 'getTotalHours']);
    Route::get('{teacher}/history', [TeacherController::class, 'getHistory']);

    // Teacher assignments
    Route::prefix('{teacher}/assignments')->group(function () {
        Route::post('classes', [TeacherAssignmentController::class, 'assignToClass']);
        Route::delete('classes/{assignment}', [TeacherAssignmentController::class, 'removeFromClass']);
        Route::patch('classes/{assignment}/status', [TeacherAssignmentController::class, 'updateAssignmentStatus']);

        Route::post('subjects', [TeacherAssignmentController::class, 'addSubject']);
        Route::delete('subjects', [TeacherAssignmentController::class, 'removeSubject']);
    });

    // Teacher Applications
    Route::prefix('applications')->group(function () {
        Route::get('/', [TeacherApplicationController::class, 'index']);
        Route::get('my', [TeacherApplicationController::class, 'myApplication']);
        Route::get('{application}', [TeacherApplicationController::class, 'show']);
        Route::post('{application}/approve', [TeacherApplicationController::class, 'approve']);
        Route::post('{application}/reject', [TeacherApplicationController::class, 'reject']);
    });
});
