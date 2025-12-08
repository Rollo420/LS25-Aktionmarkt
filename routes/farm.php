<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmController;


//Farm Routes
Route::middleware(['auth'])->group(function () {

    Route::get('/farm', [FarmController::class, 'index'])->name('farm.index');

    Route::post('/farm/send', function () {
        return back();
    })->name('farm.send');

    Route::post('/farm/{id}/accept', function () {
        return back();
    })->name('farm.accept');

    Route::post('/farm/{id}/decline', function () {
        return back();
    })->name('farm.decline');
});