<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TimeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/chart', [ChartController::class, 'show'])->name('chart.show');

Route::middleware('auth')->group(function () {
    Route::get('/time', [TimeController::class, 'index'])->name('time.index');
    Route::post('/time', [TimeController::class, 'update'])->name('time.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/stock',  [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/{id}', [ChartController::class, 'OneChart'])->name('stock.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin',  [AdminController::class, 'index'])->name('admin');
    //Route::get('/stock/{id}', [ChartController::class, 'OneChart'])->name('stock.store');
});


require __DIR__.'/auth.php';
