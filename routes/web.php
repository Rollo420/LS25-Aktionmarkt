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
    Route::post('/payment/payin', [PaymentController::class, 'payin'])->name('payment.payin');
    Route::post('/payment/payout', [PaymentController::class, 'payout'])->name('payment.payout');
    Route::post('/payment/transfer', [PaymentController::class, 'transfer'])->name('payment.transfer');
    Route::post('/payment/transaction', [PaymentController::class, 'transaction'])->name('payment.transaction');
    Route::post('/payment/buy', [PaymentController::class, 'buy'])->name('payment.buy');
    Route::post('/payment/sell', [PaymentController::class, 'sell'])->name('payment.sell');

    // Buy/Sell routes für normale User (ohne PaymentAuthorizationMiddleware)
    Route::post('/stock/{id}/payment/sellBuy', [OrderController::class, 'index'])->name('payment.SellBuy');

    Route::middleware('PaymentAuthorizationMiddleware')->group(function (){
        Route::get('/payment/auth', [PaymentAuthorizationController::class, 'index'])->name('payment.auth');

        Route::post('/payment/auth-confirmed', [PaymentAuthorizationController::class, 'handlePaymentApproval'])->name('payment.handlePaymentApproval');
    });
});

//Depot routes
Route::middleware('auth')->group(function () {
    Route::get('/depot', [DepositTransactionController::class, 'index'])->name(name: 'depot.index');
    Route::get('/stock/{id}/details', [DepositTransactionController::class, 'depotStockDetails'])->name('depot.buyDetails');
});

//Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin',  [AdminController::class, 'index'])->name('admin');
    Route::get('/admin/stock/create', [AdminController::class, 'create'])->name('admin.stock.create');
    Route::post('/admin/stock', [AdminController::class, 'store'])->name('admin.stock.store');
    Route::post('/admin/generate-field', [AdminController::class, 'generateField'])->name('admin.generate-field');
    //Route::get('/stock/{id}', [ChartController::class, 'OneChart'])->name('stock.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/update-month', [SessionController::class, 'setTimeLineMonth'])->name('update.monthTimeline');
});

require __DIR__.'/auth.php';
