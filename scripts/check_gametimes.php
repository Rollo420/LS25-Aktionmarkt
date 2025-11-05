<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GameTime;

$gts = GameTime::where('current_year', 2025)->where('month_id', 5)->get();
if ($gts->isEmpty()) {
    echo "No GameTime 05/2025 found\n";
} else {
    foreach ($gts as $g) {
        echo "ID: {$g->id} month:{$g->month_id} year:{$g->current_year} created_at: {$g->created_at}\n";
    }
}
