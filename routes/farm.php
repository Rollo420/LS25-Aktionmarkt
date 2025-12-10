<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmController;
use App\Models\Farm;

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

    Route::get('/farm/management', [FarmController::class, 'management'])->name('farm.management');

    Route::get('/farm/toggle-mode', function () {
        session(['farmMode' => !session('farmMode')]);
        return redirect()->back();
    })->name('farm.toggleMode');
});