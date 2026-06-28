<?php

use Illuminate\Support\Facades\Route;
use Modules\Grades\Controllers\GradeController;
use Modules\Grades\Controllers\SubjectController;
use Modules\Grades\Controllers\GradeAppealController;

Route::prefix('api')
    ->middleware(['api', 'auth', \Modules\Grades\Http\Middleware\GradesRateLimit::class])
    ->group(function () {
    // Subject Management
    Route::prefix('subjects')->group(function () {
        Route::get('', [SubjectController::class, 'index'])->name('subjects.index');
        Route::post('', [SubjectController::class, 'store'])->name('subjects.store');
        Route::get('{subject}', [SubjectController::class, 'show'])->name('subjects.show');
        Route::put('{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
    });

    // Grade Management
    Route::prefix('grades')->group(function () {
        Route::get('', [GradeController::class, 'index'])->name('grades.index');
        Route::post('', [GradeController::class, 'store'])->name('grades.store');
        Route::get('{grade}', [GradeController::class, 'show'])->name('grades.show');
        Route::put('{grade}', [GradeController::class, 'update'])->name('grades.update');
        Route::delete('{grade}', [GradeController::class, 'destroy'])->name('grades.destroy');
        
        Route::get('student/{student}', [GradeController::class, 'getStudentGrades'])->name('grades.student');
        Route::get('statistics', [GradeController::class, 'statistics'])->name('grades.statistics');
    });

    // Grade Appeals
    Route::prefix('grade-appeals')->group(function () {
        Route::get('', [GradeAppealController::class, 'index'])->name('grade-appeals.index');
        Route::post('', [GradeAppealController::class, 'store'])->name('grade-appeals.store');
        Route::get('my', [GradeAppealController::class, 'myAppeals'])->name('grade-appeals.my');
        
        Route::get('{appeal}', [GradeAppealController::class, 'show'])->name('grade-appeals.show');
        Route::post('{appeal}/approve', [GradeAppealController::class, 'approve'])->name('grade-appeals.approve');
        Route::post('{appeal}/reject', [GradeAppealController::class, 'reject'])->name('grade-appeals.reject');
    });
});
