<?php

use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['status' => 'ok']));

// Load module routes
require_once base_path('modules/Auth/Routes/api.php');
