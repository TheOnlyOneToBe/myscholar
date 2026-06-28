<?php

use Illuminate\Support\Facades\Route;
use Modules\Teachers\Livewire\TeacherApplicationForm;
use Modules\Teachers\Livewire\TeacherApplicationReview;
use Modules\Teachers\Livewire\TeacherSubjectManagement;
use Modules\Teachers\Models\Teacher;

Route::middleware('auth')->group(function () {
    // Teacher Application Form
    Route::get('/teacher-application', TeacherApplicationForm::class)
        ->name('teacher.application');

    // Teacher Subject Management
    Route::get('/teacher/{teacher}/subjects', function (Teacher $teacher) {
        return app(TeacherSubjectManagement::class);
    })->name('teacher.subjects');

    // Admin: Teacher Application Review
    Route::middleware('can:review-teacher-applications')->group(function () {
        Route::get('/admin/teacher-applications', TeacherApplicationReview::class)
            ->name('admin.teacher-applications');
    });
});
