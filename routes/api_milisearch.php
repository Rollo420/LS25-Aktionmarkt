<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/search/users', function () {
    $q = request('q');

    $results = \App\Models\User::search($q)
        ->take(10)
        ->get(['name', 'email'])
        ->filter(fn($user) => !str_starts_with($user->email, 'farm_'));

    return $results;
})->name('api.search.users');

Route::get('/search/stocks', function () {
    $q = request('q');

    // Komma durch Punkt ersetzen, damit 226,42 -> 226.42
    $searchQuery = str_replace(',', '.', $q);

    // Suche nach Name, Symbol oder Preis als String
    $results = \App\Models\Stock\Stock::search($searchQuery, function ($meilisearch, $query, $options) {
        $options['attributesToHighlight'] = ['name', 'firma', 'sektor', 'land', 'product_type_name', 'price_string'];
        return $meilisearch->search($query, $options);
    })
        ->take(10)
        ->get(['name', 'firma', 'sektor', 'land', 'product_type_name', 'price_string']);

    return $results;
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
