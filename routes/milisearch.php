<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/search/users', function () {
    $q = request('q');

    return User::search($q) // <--- Milisearch
        ->take(10)
        ->get(['id', 'name', 'email']);
});