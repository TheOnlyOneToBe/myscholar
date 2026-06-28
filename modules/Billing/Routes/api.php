<?php

use Illuminate\Support\Facades\Route;
use Modules\Billing\Controllers\InvoiceController;
use Modules\Billing\Controllers\PaymentController;
use Modules\Billing\Controllers\ScholarshipController;
use Modules\Billing\Controllers\FeeStructureController;

Route::middleware(['auth:sanctum'])->prefix('api/billing')->name('billing.')->group(function () {

    // Invoice Routes
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::put('{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('{invoice}', [InvoiceController::class, 'delete'])->name('delete');
        Route::post('{invoice}/overdue', [InvoiceController::class, 'markAsOverdue'])->name('mark-overdue');
        Route::get('/export', [InvoiceController::class, 'export'])->name('export');
    });

    // Payment Routes
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::post('/', [PaymentController::class, 'record'])->name('record');
        Route::get('{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
        Route::delete('{payment}', [PaymentController::class, 'delete'])->name('delete');
        Route::get('/export', [PaymentController::class, 'export'])->name('export');
    });

    // Scholarship Routes
    Route::prefix('scholarships')->name('scholarships.')->group(function () {
        Route::get('/', [ScholarshipController::class, 'index'])->name('index');
        Route::post('/', [ScholarshipController::class, 'store'])->name('store');
        Route::get('{scholarship}', [ScholarshipController::class, 'show'])->name('show');
        Route::put('{scholarship}', [ScholarshipController::class, 'update'])->name('update');
        Route::post('{scholarship}/approve', [ScholarshipController::class, 'approve'])->name('approve');
        Route::post('{scholarship}/reject', [ScholarshipController::class, 'reject'])->name('reject');
        Route::delete('{scholarship}', [ScholarshipController::class, 'delete'])->name('delete');
    });

    // Fee Structure Routes
    Route::prefix('fee-structures')->name('fee-structures.')->group(function () {
        Route::get('/', [FeeStructureController::class, 'index'])->name('index');
        Route::post('/', [FeeStructureController::class, 'store'])->name('store');
        Route::get('{feeStructure}', [FeeStructureController::class, 'show'])->name('show');
        Route::put('{feeStructure}', [FeeStructureController::class, 'update'])->name('update');
        Route::delete('{feeStructure}', [FeeStructureController::class, 'delete'])->name('delete');
        Route::post('{feeStructure}/activate', [FeeStructureController::class, 'activate'])->name('activate');
        Route::post('{feeStructure}/deactivate', [FeeStructureController::class, 'deactivate'])->name('deactivate');
    });

});
