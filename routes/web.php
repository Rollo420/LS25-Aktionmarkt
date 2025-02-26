<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usernameController;
use App\Http\Controllers\accountController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/account', [accountController::class, 'index' ]);