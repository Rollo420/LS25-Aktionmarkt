<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmController;
use Illuminate\Support\Facades\Auth;
use App\Models\Farm;

//Farm Routes
Route::middleware(['auth'])->group(function () {

    Route::get('/farm', [FarmController::class, 'index'])->name('farm.index');
    
    Route::get('/farm/new-farm', [FarmController::class, 'newFarm'])->name('farm.newFarm');
    Route::post('/farm/send-new-farm', [FarmController::class, 'sendNewFarm'])->name('farm.sendNewFarm');

    Route::get('/farm/farm-user-invite',[FarmController::class, 'farmUserInvite'])->name('farm.userInvite');

    Route::post('/farm/send-user-invite',[FarmController::class, 'sendUserInvite'])->name('farm.sendUserInvite');

    Route::get('/farm/invitations', [FarmController::class, 'invitations'])->name('farm.invitations');


    Route::post('/farm/accept', [FarmController::class, 'manageInviteAcception'])->name('farm.accept');

    Route::post('/farm/{id}/declineBTN', function () {
        return back();
    })->name('farm.declineBTN');

    Route::get('/farm/management', [FarmController::class, 'management'])->name('farm.management');

    // Farm-Verlassen Funktionalität
    Route::get('/farm/leave-confirmation', [FarmController::class, 'confirmLeaveFarm'])->name('farm.leaveConfirmation');
    Route::post('/farm/leave', [FarmController::class, 'leaveFarm'])->name('farm.leave');

    Route::get('/farm/toggle-mode', function () {

        if(Auth::user()->isInFarm())
        {
            session(['farmMode' => !session('farmMode')]);
            
            return redirect()->back()->with('success', 'Farmmodus wurde Erfolgreich gewechselt.');
        }

        session(['farmMode' => false]);
        
        return redirect()->back()->with('error', 'Farmmodus wechseln ist fehlgeschalgen.');



    })->name('farm.toggleMode');
});