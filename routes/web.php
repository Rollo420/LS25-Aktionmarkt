<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\PaymentAuthorizationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DepositTransactionController;

Route::get('/', function () {
    return view('welcome');
});

//Deashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

//Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/depot', [DepositTransactionController::class, 'depot'])->name('depot');
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
    Route::get('/stock/{id}/details', [ChartController::class, 'OneChart'])->name('stock.buyDetails');
    Route::get('/chart', [ChartController::class, 'show'])->name('chart.show');
});

//Payment routes
Route::middleware('auth')->group(function () {
    Route::get('/payment',  [PaymentController::class, 'index'])->name('payment.index');
    Route::post('/payment/payin', [PaymentController::class, 'payin'])->name('payment.payin');
    Route::post('/payment/payout', [PaymentController::class, 'payout'])->name('payment.payout');
    Route::post('/payment/transfer', [PaymentController::class, 'transfer'])->name('payment.transfer');
    Route::post('/payment/transaction', [PaymentController::class, 'transaction'])->name('payment.transaction');
    Route::post('/payment/buy', [PaymentController::class, 'buy'])->name('payment.buy');
    Route::post('/payment/sell', [PaymentController::class, 'sell'])->name('payment.sell');

    Route::middleware('PaymentAuthorizationMiddleware')->group(function (){
        Route::get('/payment/auth', [PaymentAuthorizationController::class, 'index'])->name('payment.auth');

        Route::post('/payment/auth-confirmed', [PaymentAuthorizationController::class, 'handlePaymentApproval'])->name('payment.handlePaymentApproval');

        Route::post('/stock/{id}/payment/sellBuy', [OrderController::class, 'index'])->name('payment.SellBuy');
    });
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
