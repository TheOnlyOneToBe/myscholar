<?php

use Illuminate\Support\Facades\Route;
use Modules\Classes\Controllers\ClassController;
use Modules\Classes\Controllers\RoomController;
use Modules\Classes\Controllers\ClassAssignmentController;

Route::prefix('api')->middleware(['api', 'auth'])->group(function () {
    // Rooms Management
    Route::prefix('rooms')->group(function () {
        Route::get('', [RoomController::class, 'index'])->name('rooms.index');
        Route::post('', [RoomController::class, 'store'])->name('rooms.store');
        Route::get('{room}', [RoomController::class, 'show'])->name('rooms.show');
        Route::put('{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    });

    // Classes Management
    Route::prefix('classes')->group(function () {
        Route::get('', [ClassController::class, 'index'])->name('classes.index');
        Route::post('', [ClassController::class, 'store'])->name('classes.store');
        Route::get('{class}', [ClassController::class, 'show'])->name('classes.show');
        Route::put('{class}', [ClassController::class, 'update'])->name('classes.update');
        Route::delete('{class}', [ClassController::class, 'destroy'])->name('classes.destroy');

        // Class Assignments
        Route::prefix('{class}/assignments')->group(function () {
            Route::get('', [ClassAssignmentController::class, 'indexByClass'])->name('class-assignments.index');
            Route::post('', [ClassAssignmentController::class, 'store'])->name('class-assignments.store');
            Route::put('{assignment}', [ClassAssignmentController::class, 'update'])->name('class-assignments.update');
            Route::delete('{assignment}', [ClassAssignmentController::class, 'destroy'])->name('class-assignments.destroy');
        });
    });
});
