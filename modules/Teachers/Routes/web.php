<?php

use Illuminate\Support\Facades\Route;
use Modules\Teachers\Livewire\TeacherApplicationForm;
use Modules\Teachers\Livewire\TeacherApplicationReview;
use Modules\Teachers\Livewire\TeacherSubjectManagement;
use Modules\Teachers\Livewire\TeacherCreationForm;
use Modules\Teachers\Livewire\TeacherListComponent;
use Modules\Teachers\Models\Teacher;

Route::middleware('auth')->group(function () {
    // Teacher Application Form
    Route::get('/teacher-application', TeacherApplicationForm::class)
        ->name('teacher.application');

    // Teacher Subject Management
    Route::get('/teacher/{teacher}/subjects', TeacherSubjectManagement::class)
        ->name('teacher.subjects');

    // Public: Teacher List
    Route::get('/teachers', TeacherListComponent::class)
        ->name('teachers.list');

    // Admin: Create Teacher
    Route::middleware('can:create,Modules\Teachers\Models\Teacher')->group(function () {
        Route::get('/teachers/create', TeacherCreationForm::class)
            ->name('teachers.create');
    });

    // Admin: Teacher Application Review
    Route::middleware('can:review-teacher-applications')->group(function () {
        Route::get('/admin/teacher-applications', TeacherApplicationReview::class)
            ->name('admin.teacher-applications');
    });
});
