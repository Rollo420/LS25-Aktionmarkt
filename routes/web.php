<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\SessionController;

Route::get('/', function () {
    return view('welcome');
});

//Deashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Time routes
Route::middleware(['auth', 'time'])->group(function () {
    Route::get('/time', [TimeController::class, 'index'])->name('time.index');
    Route::post('/time', [TimeController::class, 'update'])->name('time.update');
});

//Stock routes
Route::middleware('auth')->group(function () {
    Route::get('/stock',  [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/{id}', [ChartController::class, 'OneChart'])->name('stock.store');
    Route::get('/chart', [ChartController::class, 'show'])->name('chart.show');
});

//Payment routes
Route::middleware('auth')->group(function () {
    Route::get('/payment',  [PaymentController::class, 'index'])->name('payment.index');
    Route::post('/update-payment', [SessionController::class, 'setPayment'])->name('payment.updateMethod');
    Route::get('/stock/{id}', [ChartController::class, 'OneChart'])->name('stock.store');
    Route::get('/chart', [ChartController::class, 'show'])->name('chart.show');
});

//Admin routes
Route::middleware('auth')->group(function () {
    Route::get('/admin',  [AdminController::class, 'index'])->name('admin');
    //Route::get('/stock/{id}', [ChartController::class, 'OneChart'])->name('stock.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/update-month', [SessionController::class, 'setTimeLineMonth'])->name('update.monthTimeline');
});

require __DIR__.'/auth.php';
