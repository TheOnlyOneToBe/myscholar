<?php

use Illuminate\Support\Facades\Route;
use Modules\Students\Livewire\EnrollmentListComponent;

Route::middleware(['auth', 'verified'])->group(function () {
    // Enrollment Management
    Route::get('/enrollments', EnrollmentListComponent::class)->name('enrollments.list')->middleware('can:enrollments.view');
});
