<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Controllers\StudentDashboardController;

Route::middleware(['auth:sanctum'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Student Dashboard Routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/', [StudentDashboardController::class, 'index'])->name('index');
        Route::get('/grades', [StudentDashboardController::class, 'grades'])->name('grades');
        Route::get('/attendance', [StudentDashboardController::class, 'attendance'])->name('attendance');
        Route::get('/billing', [StudentDashboardController::class, 'billing'])->name('billing');
        Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
        Route::get('/chef-classe', [StudentDashboardController::class, 'chefClasseData'])->name('chef_classe');
    });
});
