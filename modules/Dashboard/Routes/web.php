<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Controllers\StudentDashboardController;
use Modules\Dashboard\Controllers\ParentDashboardController;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\ParentMiddleware;
use App\Http\Middleware\TeacherMiddleware;

Route::middleware(['web', 'auth'])->group(function () {
    // Admin Dashboard
    Route::get('/admin-dashboard', function () {
        return view('dashboard::dashboard');
    })->name('admin.dashboard');

    // Teacher Dashboard (Enseignant régulier)
    Route::middleware([TeacherMiddleware::class])->group(function () {
        Route::get('/enseignant/dashboard', function () {
            return view('dashboard::teacher-dashboard');
        })->name('teacher.dashboard');

        Route::get('/enseignant/profile', function () {
            return view('dashboard::teacher-profile');
        })->name('teacher.profile');

        Route::get('/enseignant/settings', function () {
            return view('dashboard::teacher-settings');
        })->name('teacher.settings');

        Route::get('/enseignant/classes', function () {
            return view('dashboard::teacher-classes');
        })->name('teacher.classes');

        Route::get('/enseignant/grades', function () {
            return view('dashboard::teacher-grades');
        })->name('teacher.grades');

        Route::get('/enseignant/attendance', function () {
            return view('dashboard::teacher-attendance');
        })->name('teacher.attendance');
    });

    // Head Teacher Dashboard (Professeur Principal)
    Route::middleware([TeacherMiddleware::class])->group(function () {
        Route::get('/enseignant/prof-principal/dashboard', function () {
            return view('dashboard::head-teacher-dashboard');
        })->name('head-teacher.dashboard');

        Route::get('/enseignant/prof-principal/profile', function () {
            return view('dashboard::head-teacher-profile');
        })->name('head-teacher.profile');

        Route::get('/enseignant/prof-principal/settings', function () {
            return view('dashboard::head-teacher-settings');
        })->name('head-teacher.settings');

        Route::get('/enseignant/prof-principal/class', function () {
            return view('dashboard::head-teacher-class');
        })->name('head-teacher.class');

        Route::get('/enseignant/prof-principal/class/attendance', function () {
            return view('dashboard::head-teacher-attendance');
        })->name('head-teacher.attendance');

        Route::get('/enseignant/prof-principal/class/grades', function () {
            return view('dashboard::head-teacher-grades');
        })->name('head-teacher.grades');

        Route::get('/enseignant/prof-principal/communications', function () {
            return view('dashboard::head-teacher-communications');
        })->name('head-teacher.communications');
    });

    // Student Dashboard
    Route::middleware([StudentMiddleware::class])->group(function () {
        Route::get('/student-dashboard', function () {
            return view('dashboard::student-dashboard');
        })->name('student.dashboard');

        Route::get('/student/profile', [StudentDashboardController::class, 'profile'])->name('student.profile');
        Route::get('/student/settings', [StudentDashboardController::class, 'settings'])->name('student.settings');
        Route::get('/student/help', [StudentDashboardController::class, 'help'])->name('student.help');
    });

    // Parent Dashboard
    Route::middleware([ParentMiddleware::class])->group(function () {
        Route::get('/parent-dashboard', [ParentDashboardController::class, 'dashboard'])->name('parent.dashboard');
        Route::get('/parent/profile', [ParentDashboardController::class, 'profile'])->name('parent.profile');
        Route::get('/parent/settings', [ParentDashboardController::class, 'settings'])->name('parent.settings');
    });
});
