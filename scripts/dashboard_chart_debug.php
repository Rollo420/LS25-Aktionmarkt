<?php
$require = require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\DashboardController;
use App\Services\StockService;
use App\Services\DividendeService;
use App\Models\User;
use Carbon\Carbon;

$userId = intval($argv[1] ?? 6);
$user = User::find($userId);
if (!$user) {
    echo "User {$userId} not found\n";
    exit(1);
}

$stockService = new StockService();
$divService = new DividendeService();
$stocks = $stockService->getUserStocksWithStatistiks($user);


// Re-implement minimal createChartData logic here (same as controller)
$months = 12;
$stockIds = collect($stocks)->map(fn($item) => data_get($item, 'stock.id'))->filter()->unique()->all();

$dateMap = [];
$prices = \App\Models\Stock\Price::whereIn('stock_id', $stockIds)->orderBy('created_at', 'asc')->get();
foreach ($prices as $p) {
    if (isset($p->gameTime) && $p->gameTime) {
        $year = $p->gameTime->current_year ?? date('Y');
        $month = $p->gameTime->month_id ?? 1;
    } else {
        $dt = Carbon::parse($p->created_at ?? now());
        $year = (int) $dt->format('Y');
        $month = (int) $dt->format('m');
    }
    $key = sprintf('%04d-%02d', $year, $month);
    $dateMap[$key] = Carbon::createFromDate($year, $month, 1);
}

// Determine latest price month and latest transaction month
$latestPrice = \App\Models\Stock\Price::whereIn('stock_id', $stockIds)->orderBy('created_at','desc')->first();
$latestPriceMonth = null;
if ($latestPrice) {
    if (isset($latestPrice->gameTime) && $latestPrice->gameTime) {
        $gt = $latestPrice->gameTime;
        $latestPriceMonth = Carbon::createFromDate($gt->current_year ?? date('Y'), $gt->month_id ?? 1, 1);
    } else {
        $latestPriceMonth = Carbon::parse($latestPrice->created_at ?? now())->startOfMonth();
    }
}

$latestTx = \App\Models\Stock\Transaction::where('user_id', $user->id)->orderBy('created_at','desc')->first();
$latestTxMonth = null;
if ($latestTx) {
    if (isset($latestTx->game_time_id) && $latestTx->game_time_id) {
        $gt = \App\Models\GameTime::find($latestTx->game_time_id);
        if ($gt) {
            $latestTxMonth = Carbon::createFromDate($gt->current_year ?? date('Y'), $gt->month_id ?? 1, 1);
        }
    }
    if (!$latestTxMonth) $latestTxMonth = Carbon::parse($latestTx->created_at ?? now())->startOfMonth();
}

$endMonth = collect([$latestPriceMonth, $latestTxMonth, Carbon::now()->startOfMonth()])->filter()->sortByDesc(fn($d) => $d->timestamp)->first();
if (!$endMonth) $endMonth = Carbon::now()->startOfMonth();

// Determine canonical current in-game month (most recently created GameTime) and cap endMonth to it
$gtService = new \App\Services\GameTimeService();
$latestGameTime = \App\Models\GameTime::orderBy('created_at','desc')->first();
if ($latestGameTime) {
    $latestGameTimeMonth = $gtService->toDate($latestGameTime)->startOfMonth();
    if ($latestPriceMonth) {
        $endMonth = $latestPriceMonth->lte($latestGameTimeMonth) ? $latestPriceMonth : $latestGameTimeMonth;
    } else {
        $endMonth = $latestGameTimeMonth;
    }
}

// avoid months prior to first known GameTime
$firstGameTime = \App\Models\GameTime::orderBy('created_at','asc')->first();
$firstGameTimeMonth = $firstGameTime ? $gtService->toDate($firstGameTime)->startOfMonth() : null;

// Build simulated months ending at $endMonth (ascending). Stop early if no earlier GameTime exists.
$simulatedMonths = collect();
for ($i = $months - 1; $i >= 0; $i--) {
    $candidate = $endMonth->copy()->subMonths($i);
    if ($firstGameTimeMonth && $candidate->lt($firstGameTimeMonth)) continue;
    $simulatedMonths->push($candidate);
}

$labels = $simulatedMonths->map(fn($d) => $d->format('F y'))->all();

// compute historical values (replicate getHistoricalPortfolioValues)
$transactions = \App\Models\Stock\Transaction::where('user_id', $user->id)
    ->whereIn('type', ['buy', 'sell'])
    ->orderBy('created_at')
    ->get();

$pricesByStock = \App\Models\Stock\Price::whereIn('stock_id', $stockIds)
    ->orderBy('created_at', 'asc')
    ->get()
    ->groupBy('stock_id');

$values = [];
foreach ($simulatedMonths as $monthDate) {
    $portfolioValue = 0.0;
    foreach ($stockIds as $stockId) {
        $holdings = $transactions
            ->where('stock_id', $stockId)
            ->reduce(function($carry, $tx) use ($monthDate) {
                if (isset($tx->game_time_id) && $tx->game_time_id) {
                    $gt = \App\Models\GameTime::find($tx->game_time_id);
                    if ($gt) {
                        $txMonth = Carbon::createFromDate($gt->current_year ?? date('Y'), $gt->month_id ?? 1, 1);
                    } else {
                        $txMonth = Carbon::parse($tx->created_at)->startOfMonth();
                    }
                } else {
                    $txMonth = Carbon::parse($tx->created_at)->startOfMonth();
                }
                if ($txMonth->lte($monthDate)) {
                    return $carry + ($tx->type === 'buy' ? $tx->quantity : -$tx->quantity);
                }
                return $carry;
            }, 0);

        if ($holdings <= 0) continue;

        $price = optional($pricesByStock->get($stockId))
            ->filter(function ($p) use ($monthDate) {
                if (isset($p->gameTime) && $p->gameTime) {
                    $d = Carbon::createFromDate($p->gameTime->current_year ?? date('Y'), $p->gameTime->month_id ?? 1, 1);
                    return $d->lte($monthDate);
                }
                return Carbon::parse($p->created_at ?? now())->startOfMonth()->lte($monthDate);
            })
            ->last();

        // resolve price with sensible fallbacks (exact month, latest before, earliest after, stock current)
        $priceValue = 0.0;
        $ps = collect($pricesByStock->get($stockId) ?? []);
        $exact = $ps->first(function($p) use ($monthDate) {
            if (isset($p->gameTime) && $p->gameTime) {
                $d = Carbon::createFromDate($p->gameTime->current_year ?? date('Y'), $p->gameTime->month_id ?? 1, 1);
                return $d->eq($monthDate);
            }
            return false;
        });
        if ($exact) {
            $priceValue = is_string($exact->name) ? floatval(str_replace(',', '.', $exact->name)) : (float)$exact->name;
        } else {
            $latestBefore = $ps->filter(function($p) use ($monthDate) {
                if (isset($p->gameTime) && $p->gameTime) {
                    $d = Carbon::createFromDate($p->gameTime->current_year ?? date('Y'), $p->gameTime->month_id ?? 1, 1);
                    return $d->lte($monthDate);
                }
                return Carbon::parse($p->created_at ?? now())->startOfMonth()->lte($monthDate);
            })->last();
            if ($latestBefore) {
                $priceValue = is_string($latestBefore->name) ? floatval(str_replace(',', '.', $latestBefore->name)) : (float)$latestBefore->name;
            } else {
                $earliestAfter = $ps->filter(function($p) use ($monthDate) {
                    if (isset($p->gameTime) && $p->gameTime) {
                        $d = Carbon::createFromDate($p->gameTime->current_year ?? date('Y'), $p->gameTime->month_id ?? 1, 1);
                        return $d->gt($monthDate);
                    }
                    return Carbon::parse($p->created_at ?? now())->startOfMonth()->gt($monthDate);
                })->first();
                if ($earliestAfter) {
                    $priceValue = is_string($earliestAfter->name) ? floatval(str_replace(',', '.', $earliestAfter->name)) : (float)$earliestAfter->name;
                } else {
                    $stockModel = \App\Models\Stock\Stock::find($stockId);
                    if ($stockModel) $priceValue = (float)$stockModel->getCurrentPrice();
                }
            }
        }
        if ($priceValue > 0) {
            $portfolioValue += $holdings * $priceValue;
        }
    }
    $values[] = round($portfolioValue, 2);
}

echo "Labels:\n";
foreach ($labels as $i => $lab) {
    echo sprintf("%2d: %s\n", $i+1, $lab);
}

echo "\nValues:\n";
foreach ($values as $i => $val) {
    echo sprintf("%2d: %s\n", $i+1, number_format($val, 2, ',', '.'));
}

// compute monthly P&L
$monthlyPnl = [];
for ($i = 0; $i < count($values); $i++) {
    if ($i === 0) $monthlyPnl[] = round($values[0], 2);
    else $monthlyPnl[] = round($values[$i] - $values[$i-1], 2);
}

echo "\nMonthly P&L:\n";
foreach ($monthlyPnl as $i => $val) {
    echo sprintf("%2d: %s\n", $i+1, number_format($val, 2, ',', '.'));
}

// compute monthly net investments (buys positive, sells negative) using month price
$transactions = \App\Models\Stock\Transaction::where('user_id', $user->id)->whereIn('type', ['buy','sell'])->get();
$pricesByStock = \App\Models\Stock\Price::whereIn('stock_id', $stockIds)->orderBy('created_at','asc')->get()->groupBy('stock_id');
$monthlyInvest = [];
foreach ($simulatedMonths as $monthDate) {
    $sum = 0.0;
    foreach ($transactions as $tx) {
        if (isset($tx->game_time_id) && $tx->game_time_id) {
            $gt = \App\Models\GameTime::find($tx->game_time_id);
            if ($gt) $txMonth = Carbon::createFromDate($gt->current_year ?? date('Y'), $gt->month_id ?? 1, 1);
            else $txMonth = Carbon::parse($tx->created_at)->startOfMonth();
        } else {
            $txMonth = Carbon::parse($tx->created_at)->startOfMonth();
        }
        // determine tx month (prefer game_time if available) and created_at month; match either
        $txMonthFromGame = null;
        if (isset($tx->game_time_id) && $tx->game_time_id) {
            $gt = \App\Models\GameTime::find($tx->game_time_id);
            if ($gt) $txMonthFromGame = Carbon::createFromDate($gt->current_year ?? date('Y'), $gt->month_id ?? 1, 1);
        }
        $txCreatedMonth = Carbon::parse($tx->created_at ?? now())->startOfMonth();

        $matchesMonth = false;
        if ($txMonthFromGame && $txMonthFromGame->eq($monthDate)) $matchesMonth = true;
        if ($txCreatedMonth->eq($monthDate)) $matchesMonth = true;
        if (!$matchesMonth) continue;

        // resolve price with fallbacks and prefer tx.price_at_buy as secondary fallback
        $priceVal = 0.0;
        $ps = collect($pricesByStock->get($tx->stock_id) ?? []);
        $exact = $ps->first(function($p) use ($monthDate) {
            if (isset($p->gameTime) && $p->gameTime) {
                $d = Carbon::createFromDate($p->gameTime->current_year ?? date('Y'), $p->gameTime->month_id ?? 1, 1);
                return $d->eq($monthDate);
            }
            return false;
        });
        if ($exact) {
            $priceVal = is_string($exact->name) ? floatval(str_replace(',', '.', $exact->name)) : (float)$exact->name;
        } else {
            $latestBefore = $ps->filter(function($p) use ($monthDate) {
                if (isset($p->gameTime) && $p->gameTime) {
                    $d = Carbon::createFromDate($p->gameTime->current_year ?? date('Y'), $p->gameTime->month_id ?? 1, 1);
                    return $d->lte($monthDate);
                }
                return Carbon::parse($p->created_at ?? now())->startOfMonth()->lte($monthDate);
            })->last();
            if ($latestBefore) {
                $priceVal = is_string($latestBefore->name) ? floatval(str_replace(',', '.', $latestBefore->name)) : (float)$latestBefore->name;
            } else {
                $earliestAfter = $ps->filter(function($p) use ($monthDate) {
                    if (isset($p->gameTime) && $p->gameTime) {
                        $d = Carbon::createFromDate($p->gameTime->current_year ?? date('Y'), $p->gameTime->month_id ?? 1, 1);
                        return $d->gt($monthDate);
                    }
                    return Carbon::parse($p->created_at ?? now())->startOfMonth()->gt($monthDate);
                })->first();
                if ($earliestAfter) {
                    $priceVal = is_string($earliestAfter->name) ? floatval(str_replace(',', '.', $earliestAfter->name)) : (float)$earliestAfter->name;
                } else {
                    // fallback to tx.price_at_buy if available
                    if (isset($tx->price_at_buy) && $tx->price_at_buy !== null) {
                        $priceVal = (float)$tx->price_at_buy;
                    } else {
                        $stockModel = \App\Models\Stock\Stock::find($tx->stock_id);
                        if ($stockModel) $priceVal = (float)$stockModel->getCurrentPrice();
                    }
                }
            }
        }
        $qty = $tx->quantity * ($tx->type === 'buy' ? 1 : -1);
        $sum += $qty * $priceVal;
    }
    $monthlyInvest[] = round($sum,2);
}

echo "\nMonthly Net Invest (buy positive, sell negative):\n";
foreach ($monthlyInvest as $i => $val) {
    echo sprintf("%2d: %s\n", $i+1, number_format($val, 2, ',', '.'));
}

exit(0);
