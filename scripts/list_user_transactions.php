<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Stock\Transaction;
use App\Models\Stock\Price;
use App\Models\User;
use App\Models\GameTime;

$userId = $argv[1] ?? 1;
$user = User::find($userId);
if (!$user) { echo "User {$userId} not found\n"; exit(1); }

$txs = Transaction::where('user_id', $userId)->whereIn('type', ['buy','sell'])->orderBy('created_at')->get();
if ($txs->isEmpty()) { echo "No buy/sell transactions for user {$userId}\n"; exit(0); }

foreach ($txs as $t) {
    $gt = $t->game_time_id ? GameTime::find($t->game_time_id) : null;
    $price = Price::where('stock_id', $t->stock_id)
        ->where(function($q) use ($t) {
            if ($t->game_time_id) {
                $q->where('game_time_id', $t->game_time_id);
            } else {
                $q->where('created_at', '<=', $t->created_at);
            }
        })->orderBy('created_at','desc')->first();

    echo sprintf("Tx ID:%d type:%s stock:%s qty:%s price_at_buy:%s game_time_id:%s gt_label:%s created_at:%s\n",
        $t->id, $t->type, $t->stock_id, $t->quantity, $t->price_at_buy ?? 'NULL', $t->game_time_id ?? 'NULL',
        $gt ? ($gt->month_id . '/' . $gt->current_year) : 'no-gt', $t->created_at
    );
}
