<?php

use Illuminate\Support\Facades\Route;

//My controllers
use App\Http\Controllers\usernameController;
use App\Http\Controllers\accountController;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/account', [accountController::class, 'index' ]);

Route::get('/stock', [StockController::class, 'index' ]);