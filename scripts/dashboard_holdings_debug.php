<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Stock\Transaction;
use App\Models\Stock\Price;
use App\Services\StockService;
use Carbon\Carbon;

$userId = intval($argv[1] ?? 6);
$user = User::find($userId);
if (!$user) { echo "User {$userId} not found\n"; exit(1); }

// Build months same as controller
$months = 12;
$stockService = new StockService();
$stocks = $stockService->getUserStocksWithStatistiks($user);
$stockIds = collect($stocks)->map(fn($item) => data_get($item, 'stock.id'))->filter()->unique()->all();

$dateMap = [];
$prices = \App\Models\Stock\Price::whereIn('stock_id', $stockIds)->orderBy('created_at', 'asc')->get();
foreach ($prices as $p) {
    if (isset($p->gameTime) && $p->gameTime) {
        $y = $p->gameTime->current_year ?? date('Y');
        $m = $p->gameTime->month_id ?? 1;
    } else {
        $dt = Carbon::parse($p->created_at ?? now());
        $y = (int)$dt->format('Y');
        $m = (int)$dt->format('m');
    }
    $k = sprintf('%04d-%02d', $y, $m);
    $dateMap[$k] = Carbon::createFromDate($y, $m, 1);
}
// include tx months
$txMonths = Transaction::where('user_id', $user->id)->whereIn('type',['buy','sell'])->get();
foreach ($txMonths as $tx) {
    if ($tx->game_time_id) {
        $gt = \App\Models\GameTime::find($tx->game_time_id);
        if ($gt) { $y = $gt->current_year; $m = $gt->month_id; }
        else { $dt = Carbon::parse($tx->created_at); $y = (int)$dt->format('Y'); $m = (int)$dt->format('m'); }
    } else { $dt = Carbon::parse($tx->created_at); $y = (int)$dt->format('Y'); $m = (int)$dt->format('m'); }
    $dateMap[sprintf('%04d-%02d',$y,$m)] = Carbon::createFromDate($y,$m,1);
}

// determine endMonth
$latestPrice = \App\Models\Stock\Price::whereIn('stock_id',$stockIds)->orderBy('created_at','desc')->first();
$latestPriceMonth = $latestPrice ? (isset($latestPrice->gameTime)&&$latestPrice->gameTime?Carbon::createFromDate($latestPrice->gameTime->current_year,$latestPrice->gameTime->month_id,1):Carbon::parse($latestPrice->created_at)->startOfMonth()) : null;
$latestTx = Transaction::where('user_id',$user->id)->orderBy('created_at','desc')->first();
$latestTxMonth = null; if ($latestTx) { if ($latestTx->game_time_id) { $gt = \App\Models\GameTime::find($latestTx->game_time_id); if($gt) $latestTxMonth = Carbon::createFromDate($gt->current_year,$gt->month_id,1);} if(!$latestTxMonth) $latestTxMonth = Carbon::parse($latestTx->created_at)->startOfMonth(); }
$endMonth = collect([$latestPriceMonth,$latestTxMonth,Carbon::now()->startOfMonth()])->filter()->sortByDesc(fn($d)=>$d->timestamp)->first(); if(!$endMonth) $endMonth = Carbon::now()->startOfMonth();

$simulatedMonths = collect(); for($i=$months-1;$i>=0;$i--){ $simulatedMonths->push($endMonth->copy()->subMonths($i)); }

$transactions = Transaction::where('user_id',$user->id)->whereIn('type',['buy','sell'])->orderBy('created_at')->get();
$pricesByStock = Price::whereIn('stock_id',$stockIds)->orderBy('created_at','asc')->get()->groupBy('stock_id');

echo "Debug holdings for user {$userId}\n";
foreach ($simulatedMonths as $monthDate) {
    echo "Month: " . $monthDate->format('F Y') . "\n";
    foreach ($stockIds as $stockId) {
        $holdings = $transactions->where('stock_id',$stockId)->reduce(function($carry,$tx) use ($monthDate){
            if ($tx->game_time_id) { $gt = \App\Models\GameTime::find($tx->game_time_id); if($gt) $txMonth = Carbon::createFromDate($gt->current_year,$gt->month_id,1); else $txMonth = Carbon::parse($tx->created_at)->startOfMonth(); } else { $txMonth = Carbon::parse($tx->created_at)->startOfMonth(); }
            if ($txMonth->lte($monthDate)) return $carry + ($tx->type==='buy'?$tx->quantity:-$tx->quantity);
            return $carry;
        },0);

        $price = optional($pricesByStock->get($stockId))->filter(function($p) use ($monthDate){ if(isset($p->gameTime)&&$p->gameTime){ $d = Carbon::createFromDate($p->gameTime->current_year,$p->gameTime->month_id,1); return $d->lte($monthDate);} return Carbon::parse($p->created_at)->startOfMonth()->lte($monthDate); })->last();
        $priceVal = $price ? (is_string($price->name)?floatval(str_replace(',','.',$price->name)):(float)$price->name) : 0;
        echo sprintf("  Stock %d holdings: %d price_used: %s => value: %s\n", $stockId, $holdings, number_format($priceVal,2,',','.'), number_format($holdings*$priceVal,2,',','.'));
    }
    echo "\n";
}

exit(0);
