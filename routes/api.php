<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Register users API resource so /api/users routes are available
Route::apiResource('users', UserController::class);
