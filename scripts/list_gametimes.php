<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GameTime;
use App\Models\Stock\Price;

$gts = GameTime::orderBy('current_year')->orderBy('month_id')->get();
echo "GameTime rows: " . count($gts) . PHP_EOL;
foreach ($gts as $gt) {
    $count = Price::where('game_time_id', $gt->id)->count();
    $label = null;
    try {
        if (method_exists($gt, 'toDate')) {
            $label = date('F y', strtotime($gt->toDate()));
        } else {
            $label = date('F y', strtotime("{$gt->current_year}-{$gt->month_id}-01"));
        }
    } catch (Throwable $e) {
        $label = "{$gt->month_id}/{$gt->current_year}";
    }
    echo "ID:{$gt->id} month:{$gt->month_id} year:{$gt->current_year} label:{$label} prices:{$count}" . PHP_EOL;
}
