<?php

use Illuminate\Support\Facades\Route;

//My controllers
use App\Http\Controllers\usernameController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin', [AdminController::class, 'index' ]);

Route::get('/account', [AccountController::class, 'index' ]);

Route::get('/stock', [StockController::class, 'index' ]);
