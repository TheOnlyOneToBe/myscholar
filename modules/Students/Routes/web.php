<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Web routes for Students module will be added here when Livewire components are created
    // Example:
    // Route::get('/students', StudentListComponent::class)->name('students.list')->middleware('can:students.view');
    // Route::get('/students/{student}', StudentDetailComponent::class)->name('students.detail')->middleware('can:view,student');
});
