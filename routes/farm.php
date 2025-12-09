<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmController;


//Farm Routes
Route::middleware(['auth'])->group(function () {

    Route::get('/farm', [FarmController::class, 'index'])->name('farm.index');

    Route::get('/farm/farm-user-invite',[FarmController::class, 'farmUserInvite'])->name('farm.userInvite');

    Route::post('/farm/send-user-invite',[FarmController::class, 'sendUserInvite'])->name('farm.sendUserInvite');

    Route::post('/farm/{id}/accept', function () {
        return back();
    })->name('farm.accept');

    Route::post('/farm/{id}/decline', function () {
        return back();
    })->name('farm.decline');
});