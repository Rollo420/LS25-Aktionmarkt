<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Stock\Price;
use App\Models\GameTime;

$stockId = $argv[1] ?? 1;
$prices = Price::where('stock_id', $stockId)->orderBy('game_time_id')->orderBy('created_at')->get();

if ($prices->isEmpty()) {
    echo "No prices found for stock {$stockId}\n";
    exit;
}

foreach ($prices as $p) {
    $gt = GameTime::find($p->game_time_id);
    $label = $gt ? (method_exists($gt, 'toDate') ? date('F y', strtotime($gt->toDate())) : "{$gt->month_id}/{$gt->current_year}") : 'no-game-time';
    echo "Price ID: {$p->id} stock:{$p->stock_id} game_time_id:{$p->game_time_id} label:{$label} name:{$p->name} created_at:{$p->created_at}\n";
}
