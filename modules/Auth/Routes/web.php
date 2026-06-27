<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Livewire\LoginComponent;
use Modules\Auth\Livewire\RegisterComponent;
use Modules\Auth\Livewire\ForgotPasswordComponent;
use Modules\Auth\Livewire\ResetPasswordComponent;
use Modules\Auth\Livewire\DashboardComponent;

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginComponent::class)->name('login');
    Route::get('/register', RegisterComponent::class)->name('register')->middleware('throttle:10,1');
    Route::get('/forgot-password', ForgotPasswordComponent::class)->name('password.request')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', ResetPasswordComponent::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardComponent::class)->name('dashboard');
    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
