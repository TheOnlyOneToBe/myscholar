<?php

use Illuminate\Support\Facades\Route;
use Modules\Audit\Livewire\AuditLogComponent;

Route::middleware(['auth'])->group(function () {
    Route::get('/audit/logs', AuditLogComponent::class)->name('audit.logs')->middleware('can:audit.view');
});
