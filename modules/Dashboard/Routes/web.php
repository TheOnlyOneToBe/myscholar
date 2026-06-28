<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Controllers\StudentDashboardController;
use Modules\Dashboard\Controllers\ParentDashboardController;

Route::middleware(['web', 'auth'])->group(function () {
    // Admin Dashboard
    Route::get('/admin-dashboard', function () {
        return view('dashboard::dashboard');
    })->name('admin.dashboard');

    // Student Dashboard
    Route::middleware(['student'])->group(function () {
        Route::get('/student-dashboard', function () {
            return view('dashboard::student-dashboard');
        })->name('student.dashboard');

        Route::get('/student/profile', [StudentDashboardController::class, 'profile'])->name('student.profile');
        Route::get('/student/settings', [StudentDashboardController::class, 'settings'])->name('student.settings');
        Route::get('/student/help', [StudentDashboardController::class, 'help'])->name('student.help');
    });

    // Parent Dashboard
    Route::middleware(['parent'])->group(function () {
        Route::get('/parent-dashboard', [ParentDashboardController::class, 'dashboard'])->name('parent.dashboard');
        Route::get('/parent/profile', [ParentDashboardController::class, 'profile'])->name('parent.profile');
        Route::get('/parent/settings', [ParentDashboardController::class, 'settings'])->name('parent.settings');
    });
});
