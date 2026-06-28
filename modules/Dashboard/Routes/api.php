<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Controllers\StudentDashboardController;
use Modules\Dashboard\Controllers\DocumentDownloadController;
use Modules\Dashboard\Controllers\BulletinController;
use Modules\Dashboard\Controllers\TermDocumentController;

Route::middleware(['auth:sanctum'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Student Dashboard Routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/', [StudentDashboardController::class, 'index'])->name('index');
        Route::get('/grades', [StudentDashboardController::class, 'grades'])->name('grades');
        Route::get('/attendance', [StudentDashboardController::class, 'attendance'])->name('attendance');
        Route::get('/billing', [StudentDashboardController::class, 'billing'])->name('billing');
        Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
        Route::get('/chef-classe', [StudentDashboardController::class, 'chefClasseData'])->name('chef_classe');
    });

    // Bulletin Routes
    Route::prefix('bulletins')->name('bulletin.')->group(function () {
        Route::get('/download/{term?}', [BulletinController::class, 'downloadBulletin'])->name('download');
        Route::get('/complete', [BulletinController::class, 'downloadCompleteBulletin'])->name('complete');
        Route::get('/preview/{term?}', [BulletinController::class, 'previewBulletin'])->name('preview');
        Route::get('/complete/preview', [BulletinController::class, 'previewCompleteBulletin'])->name('complete.preview');
    });

    // Document Download Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/certificate/{academicYearId}', [DocumentDownloadController::class, 'schoolCertificate'])->name('certificate');
        Route::get('/report-card/{academicYearId}', [DocumentDownloadController::class, 'reportCard'])->name('report-card');
        Route::get('/transcript', [DocumentDownloadController::class, 'transcript'])->name('transcript');
        Route::get('/enrollment-summary', [DocumentDownloadController::class, 'enrollmentSummary'])->name('enrollment-summary');
        Route::get('/invoice/{invoiceId}', [DocumentDownloadController::class, 'invoice'])->name('invoice');
    });

    // Term-Based Documents Routes (Bulletins, relevés par trimestre)
    Route::prefix('term-documents')->name('term-documents.')->group(function () {
        Route::get('/terms', [TermDocumentController::class, 'getAvailableTerms'])->name('terms');
        Route::get('/student/{studentId}/bulletin/{academicPeriodId}', [TermDocumentController::class, 'getTermBulletinData'])->name('bulletin.data');
        Route::get('/student/{studentId}/bulletin/{academicPeriodId}/download', [TermDocumentController::class, 'downloadTermBulletin'])->name('bulletin.download');
        Route::get('/student/{studentId}/bulletin/{academicPeriodId}/preview', [TermDocumentController::class, 'previewTermBulletin'])->name('bulletin.preview');
        Route::get('/student/{studentId}/transcript', [TermDocumentController::class, 'getTermTranscript'])->name('transcript.data');
        Route::get('/student/{studentId}/transcript/download', [TermDocumentController::class, 'downloadTermTranscript'])->name('transcript.download');
        Route::get('/class/{classId}/summary/{academicPeriodId}', [TermDocumentController::class, 'getTermClassSummary'])->name('class-summary.data');
        Route::get('/class/{classId}/summary/{academicPeriodId}/download', [TermDocumentController::class, 'downloadTermClassSummary'])->name('class-summary.download');
    });
});
