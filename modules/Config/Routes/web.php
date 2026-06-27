<?php

use Illuminate\Support\Facades\Route;
use Modules\Config\Livewire\DetailComponent;
use Modules\Config\Livewire\FooterComponent;
use Modules\Config\Livewire\SchoolYearComponent;

Route::middleware(['web', 'auth'])->group(function () {
    // Configuration detail page
    Route::get('/config', DetailComponent::class)
        ->name('config.detail')
        ->middleware('can:config.view');

    // School Years Management
    Route::get('/config/school-years', SchoolYearComponent::class)
        ->name('config.school-years')
        ->middleware('can:config.school_year.view');
});

// Footer component is available globally (no auth required)
