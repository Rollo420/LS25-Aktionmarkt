<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Stock\Stock;
use Carbon\Carbon;

$stockId = $argv[1] ?? 1;
$stock = Stock::with('prices.gameTime')->find($stockId);
if (!$stock) {
    echo "Stock id {$stockId} not found\n";
    exit(1);
}

function buildSeriesForLimit($stock, $limit)
{
    $allPrices = $stock->prices()->with('gameTime')->get();
    $groupedByGameTime = [];

    foreach ($allPrices as $price) {
        $gt = $price->gameTime;
        if (!$gt) {
            continue; // ignore prices without gameTime for in-game months
        }
        $key = ($gt->current_year ?? date('Y')) . '-' . str_pad($gt->month_id ?? 1, 2, '0', STR_PAD_LEFT);
        $ts = $price->created_at ? strtotime($price->created_at) : Carbon::createFromDate($gt->current_year ?? date('Y'), $gt->month_id ?? 1, 1)->timestamp;
        if (!isset($groupedByGameTime[$key]) || $ts >= ($groupedByGameTime[$key]['ts'] ?? 0)) {
            $raw = $price->name ?? 0;
            $num = is_string($raw) ? floatval(str_replace(',', '.', $raw)) : (float)$raw;
            $groupedByGameTime[$key] = [
                'price_obj' => $price,
                'price' => $num,
                'ts' => $ts,
                'game_time' => $gt,
            ];
        }
    }

    // to array and sort by gameTime chronological order
    $groupItems = [];
    foreach ($groupedByGameTime as $key => $entry) {
        [$yr, $mo] = explode('-', $key);
        $groupItems[] = array_merge($entry, ['yr' => intval($yr), 'mo' => intval($mo)]);
    }

    usort($groupItems, function ($a, $b) {
        if ($a['yr'] === $b['yr']) return $a['mo'] <=> $b['mo'];
        return $a['yr'] <=> $b['yr'];
    });

    $selected = array_slice($groupItems, -1 * (int)$limit, (int)$limit, true);

    $points = [];
    foreach ($selected as $entry) {
        $ts = $entry['ts'] ?? 0;
        $points[] = [
            'ts' => $ts,
            'price' => $entry['price'],
            'created_at' => $entry['price_obj']->created_at,
            'game_time' => $entry['game_time'],
        ];
    }

    usort($points, function ($a, $b) {
        return ($a['ts'] ?? 0) <=> ($b['ts'] ?? 0);
    });

    $currentPrice = $stock->getCurrentPrice();
    $lastValue = null;
    if (!empty($points)) {
        $lastValue = isset($points[count($points) - 1]['price']) ? (float)$points[count($points) - 1]['price'] : null;
    }

    if ($lastValue === null || (float)$lastValue !== (float)$currentPrice) {
        $latestPrice = $stock->prices()->with('gameTime')->orderBy('created_at', 'desc')->first();
        $currentGameTime = $latestPrice->gameTime ?? null;
        $maxTs = empty($points) ? time() : max(array_column($points, 'ts'));
        $points[] = [
            'ts' => ($maxTs ?? time()) + 1,
            'price' => (float)$currentPrice,
            'created_at' => now()->toDateTimeString(),
            'game_time' => $currentGameTime,
        ];
        while (count($points) > (int) $limit) {
            array_shift($points);
        }
        usort($points, function ($a, $b) {
            return ($a['ts'] ?? 0) <=> ($b['ts'] ?? 0);
        });
    }

    // build labels and values
    $labels = [];
    $values = [];
    foreach ($points as $k => $v) {
        $gt = $v['game_time'];
        if ($gt) {
            $monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            $m = $gt->month_id ?? 1;
            $yr = $gt->current_year ?? date('Y');
            $label = ($monthNames[$m - 1] ?? 'Month') . ' ' . substr((string)$yr, -2);
        } else {
            $label = date('d.m.Y H:i', strtotime($v['created_at']));
        }
        $labels[] = $label;
        $values[] = $v['price'];
    }

    return ['labels' => $labels, 'values' => $values, 'currentPrice' => (float)$currentPrice];
}

$limits = [3,6,12,24];
foreach ($limits as $l) {
    $res = buildSeriesForLimit($stock, $l);
    echo "--- LIMIT {$l} ---\n";
    echo "currentPrice: " . number_format($res['currentPrice'], 2, ',', '.') . "\n";
    foreach ($res['labels'] as $i => $lab) {
        echo sprintf("%2d: %s => %s\n", $i+1, $lab, number_format($res['values'][$i], 2, ',', '.'));
    }
    echo "\n";
}

exit(0);
