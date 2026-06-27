<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestAlertController;

Route::prefix('test')->group(function () {
    Route::get('/alerts', [TestAlertController::class, 'showForm'])->name('test.alerts');
    Route::post('/alert-success', [TestAlertController::class, 'triggerSuccess'])->name('test.alert-success');
    Route::post('/alert-warning', [TestAlertController::class, 'triggerWarning'])->name('test.alert-warning');
    Route::post('/alert-error', [TestAlertController::class, 'triggerError'])->name('test.alert-error');
    Route::post('/alert-multiple', [TestAlertController::class, 'triggerMultiple'])->name('test.alert-multiple');
});
