<?php

use Illuminate\Support\Facades\Route;
use Modules\Config\Livewire\DetailComponent;
use Modules\Config\Livewire\FooterComponent;

Route::middleware(['web', 'auth'])->group(function () {
    // Configuration detail page
    Route::get('/config', DetailComponent::class)
        ->name('config.detail')
        ->middleware('can:config.view');
});

// Footer component is available globally (no auth required)
