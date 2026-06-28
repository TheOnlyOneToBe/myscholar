<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard::dashboard');
    })->name('dashboard');
});
