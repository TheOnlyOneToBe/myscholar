<?php

use Illuminate\Support\Facades\Route;

Route::prefix('classes')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', function () {
        return view('classes::pages.index');
    })->name('classes.index');

    Route::get('/rooms', function () {
        return view('classes::pages.rooms');
    })->name('classes.rooms');

    Route::get('{class}/assignments', function ($class) {
        return view('classes::pages.assignments', ['class' => $class]);
    })->name('classes.assignments');

    Route::get('{class}/timetable', function ($class) {
        return view('classes::pages.timetable', ['class' => $class]);
    })->name('classes.timetable');

    Route::get('/dashboard', function () {
        return view('classes::pages.dashboard');
    })->name('classes.dashboard');
});
