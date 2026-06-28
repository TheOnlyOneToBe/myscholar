<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Controllers\StudentDashboardController;

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
});
