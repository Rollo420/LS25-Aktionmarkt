<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/search/users', function () {
    $q = request('q');

    return \App\Models\User::search($q)
        ->take(10)
        ->get(['id', 'name', 'email']);
})->name('api.search.users');

Route::get('/search/stocks', function () {
    $q = request('q');

    return \App\Models\Stock\Stock::search($q)
        ->take(10)
        ->get(['id', 'name', 'symbol']);
})->name('api.search.stocks');

Route::get('/search/product-types', function () {
    $q = request('q');

    return \App\Models\ProductType::search($q)
        ->take(10)
        ->get(['id', 'name']);
})->name('api.search.product-types');

Route::get('/search/farms', function () {
    $q = request('q');

    return \App\Models\Farm::search($q)
        ->take(10)
        ->get(['id', 'name', 'email']);
})->name('api.search.farms');
