<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin-dashboard', function () {
        return view('dashboard::dashboard');
    })->name('admin.dashboard');
});
