<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Test Alert System Routes
require __DIR__ . '/test-alerts.php';
