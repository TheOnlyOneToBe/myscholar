<?php

use Illuminate\Support\Facades\Route;

Route::prefix('classes')->middleware('web')->group(function () {
    // Routes web pour le module (à implémenter selon les besoins)
});
