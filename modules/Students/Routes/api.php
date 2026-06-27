<?php

use Illuminate\Support\Facades\Route;
use Modules\Students\Controllers\StudentController;
use Modules\Students\Controllers\StudentIdFormatController;

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    // Student ID Format Configuration
    Route::prefix('students/id-format')->group(function () {
        Route::get('', [StudentIdFormatController::class, 'show'])->name('students.id-format.show');
        Route::put('', [StudentIdFormatController::class, 'update'])->name('students.id-format.update')->middleware('can:config.edit');
        Route::get('available-elements', [StudentIdFormatController::class, 'getAvailableElements'])->name('students.id-format.available-elements');
        Route::post('preview', [StudentIdFormatController::class, 'preview'])->name('students.id-format.preview');
    });

    // Student CRUD and related endpoints
    Route::prefix('students')->group(function () {
        Route::get('', [StudentController::class, 'index'])->name('students.index')->middleware('can:students.view');
        Route::post('', [StudentController::class, 'store'])->name('students.store')->middleware('can:students.create');
        Route::get('export', [StudentController::class, 'export'])->name('students.export')->middleware('can:students.export');

        Route::get('{student}', [StudentController::class, 'show'])->name('students.show')->middleware('can:view,student');
        Route::put('{student}', [StudentController::class, 'update'])->name('students.update')->middleware('can:update,student');
        Route::delete('{student}', [StudentController::class, 'destroy'])->name('students.destroy')->middleware('can:delete,student');

        // Student Actions
        Route::post('{student}/suspend', [StudentController::class, 'suspend'])->name('students.suspend')->middleware('can:suspend,student');
        Route::post('{student}/activate', [StudentController::class, 'activate'])->name('students.activate')->middleware('can:activate,student');

        // Student Relations
        Route::get('{student}/contacts', [StudentController::class, 'getContacts'])->name('students.contacts');
        Route::get('{student}/enrollments', [StudentController::class, 'getEnrollments'])->name('students.enrollments');
        Route::get('{student}/family-contacts', [StudentController::class, 'getFamilyContacts'])->name('students.family-contacts');
        Route::get('{student}/history', [StudentController::class, 'getHistory'])->name('students.history');
    });
});
