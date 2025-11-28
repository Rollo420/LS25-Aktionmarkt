<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DividendController;;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\FarmController;

//Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Redirect /admin zur Benutzer-Verwaltung
    Route::redirect('/admin', '/admin/users');

    // Time management routes
    Route::get('/admin/time', [TimeController::class, 'index'])->name('admin.time.index');
    Route::post('/admin/time', [TimeController::class, 'update'])->name('admin.time.update');

    Route::get('/admin/stock/create', [AdminController::class, 'create'])->name('admin.stock.create');
    Route::post('/admin/stock', [AdminController::class, 'store'])->name('admin.stock.store');
    Route::post('/admin/generate-field', [AdminController::class, 'generateField'])->name('admin.generate-field');

    // User management routes
    Route::get('/admin/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
    Route::get('/admin/users/{user}', [AdminController::class, 'usersShow'])->name('admin.users.show');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'usersUpdate'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'usersDestroy'])->name('admin.users.destroy');

    // Stock management routes
    Route::get('/admin/stocks', [AdminController::class, 'stocksIndex'])->name('admin.stocks.index');
    Route::get('/admin/stocks/{stock}', [AdminController::class, 'stocksShow'])->name('admin.stocks.show');
    Route::get('/admin/stocks/{stock}/edit', [AdminController::class, 'stocksEdit'])->name('admin.stocks.edit');
    Route::put('/admin/stocks/{stock}', [AdminController::class, 'stocksUpdate'])->name('admin.stocks.update');
    Route::delete('/admin/stocks/{stock}', [AdminController::class, 'stocksDestroy'])->name('admin.stocks.destroy');

    // Game time management routes
    Route::get('/admin/game-times', [AdminController::class, 'gameTimesIndex'])->name('admin.game-times.index');
    Route::get('/admin/game-times/{gameTime}', [AdminController::class, 'gameTimesShow'])->name('admin.game-times.show');
    Route::get('/admin/game-times/{gameTime}/edit', [AdminController::class, 'gameTimesEdit'])->name('admin.game-times.edit');
    Route::put('/admin/game-times/{gameTime}', [AdminController::class, 'gameTimesUpdate'])->name('admin.game-times.update');
    Route::delete('/admin/game-times/{gameTime}', [AdminController::class, 'gameTimesDestroy'])->name('admin.game-times.destroy');

    // Dividend management routes
    Route::get('/admin/dividends', [DividendController::class, 'index'])->name('admin.dividends.index');
    Route::get('/admin/dividends/{dividend}', [DividendController::class, 'show'])->name('admin.dividends.show');
    Route::get('/admin/dividends/{dividend}/edit', [DividendController::class, 'edit'])->name('admin.dividends.edit');
    Route::put('/admin/dividends/{dividend}', [DividendController::class, 'update'])->name('admin.dividends.update');
    Route::delete('/admin/dividends/{dividend}', [DividendController::class, 'destroy'])->name('admin.dividends.destroy');

    // Config routes
    Route::get('/admin/configs', [ConfigController::class, 'index'])->name('admin.configs.index');
    Route::get('/admin/configs/create', [ConfigController::class, 'create'])->name('admin.configs.create');
    Route::post('/admin/configs', [ConfigController::class, 'store'])->name('admin.configs.store');
    Route::get('/admin/configs/{config}', [ConfigController::class, 'show'])->name('admin.configs.show');
    Route::get('/admin/configs/{config}/edit', [ConfigController::class, 'edit'])->name('admin.configs.edit');
    Route::put('/admin/configs/{config}', [ConfigController::class, 'update'])->name('admin.configs.update');
    Route::delete('/admin/configs/{config}', [ConfigController::class, 'destroy'])->name('admin.configs.destroy');

    // Farm management routes
    Route::get('/admin/farm/create', function () {
        return view('admin.farm.create');
    })->middleware(['auth'])->name('admin.farm.create');

    Route::post('/admin/farm/store', function () {
        // Controller später
    })->middleware(['auth'])->name('admin.farm.store');
});
