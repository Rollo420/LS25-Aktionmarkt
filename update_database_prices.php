<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Aktualisiere alle bestehenden Preise in der Datenbank auf den neuen Bereich (30.000-800.000)...\n";

$prices = \App\Models\Stock\Price::all();
$updated = 0;

foreach ($prices as $price) {
    $oldPrice = $price->name;
    $newPrice = fake()->randomFloat(2, 30000, 800000);
    $price->update(['name' => $newPrice]);
    $updated++;
    echo "Updated Price ID {$price->id}: {$oldPrice} → {$newPrice}\n";
}

echo "Erfolgreich {$updated} Preise aktualisiert!\n";

echo "\nAktualisiere auch die Startpreise der Aktien...\n";

$stocks = \App\Models\Stock\Stock::all();
foreach ($stocks as $stock) {
    $oldStartPrice = $stock->start_price;
    $newStartPrice = fake()->randomFloat(2, 30000, 800000);
    $stock->update(['start_price' => $newStartPrice]);
    echo "Updated Stock {$stock->id} start_price: {$oldStartPrice} → {$newStartPrice}\n";
}

echo "Alle Datenbank-Updates abgeschlossen!\n";
