<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\StockService;
use App\Services\DividendeService;
use Carbon\Carbon;

use App\Models\Stock\{Stock, Transaction, Price};
use App\Models\{BuyTransaction, GameTime};
use App\Services\GameTimeService;
use App\Services\PriceResolverService;

class DashboardController extends Controller
{
    public function index(StockService $stockService, DividendeService $dividendeService)
    {
        $user = Auth::user();

        $stocks = $stockService->getUserStocksWithStatistiks($user);

        $depotInfo['totalPortfolioValue'] = $stockService->getTotalPortfolioValue();

        // Top/Flop Aktien - exklusiv, keine Überlappung
        $sortedStocks = $stocks->sortByDesc('profit_loss_percent');
        $topThreeUp = $sortedStocks->take(3);
        $remainingStocks = $sortedStocks->slice(3);
        $topThreeDown = $remainingStocks->sortBy('profit_loss_percent')->take(3);

        $depotInfo['tops'] = [
            'topThreeUp' => $topThreeUp->values(),
            'topThreeDown' => $topThreeDown->values(),
        ];

        // Letzte 5 Transaktionen - Eager Loading für bessere Performance
        $depotInfo['lastTransactions'] = Transaction::where('user_id', $user->id)
            ->with(['stock:id,name', 'gameTime:id,name']) // Nur benötigte Felder laden
            ->latest()
            ->take(5)
            ->get();


        $dividendeService = new DividendeService();

        $depotInfo['nextDividends'] = collect($stocks)
            ->filter(fn($item) => $item->stock !== null)
            ->sortByDesc(fn($item) => $item->stock->calculateNextDividendDate())
            ->map(function ($item) use ($dividendeService) {
                $stock = $item->stock;
                $divData = $dividendeService->getDividendStatisticsForStock($stock);

                return [
                    'name' => $stock->name,
                    'next_dividend' => $divData->next_date,
                    'price' => $stock->getLatestPrice(),                        // aktueller Kurs (€)
                    'dividend' => $divData->dividendPerShare, // Dividende (€)
                    'percent' => $divData->dividendPercent,    // Rendite (%)
                ];
            })
            ->values();

        $depotInfo['averages'] = [
            'avg_stock_price_eur' => round($depotInfo['nextDividends']->avg('price'), 2),        // Durchschnittlicher Aktienkurs (€)
            'avg_dividend_amount_eur' => round($depotInfo['nextDividends']->avg('dividend'), 2),    // Durchschnittliche Dividende (€)
            'avg_dividend_percent_total' => round($depotInfo['nextDividends']->avg('percent'), 2),     // Durchschnittliche Dividendenrendite (%)
        ];


        // Nur die Top 5 nächsten Dividenden anzeigen
        $depotInfo['nextDividends'] = $depotInfo['nextDividends']->take(5)->toArray();


        // Performance-Metriken (3-Monats, 6-Monats & Benchmark)
        $depotInfo["monthly_performance"] = $this->calculatePortfolioPerformance($stocks, $user);

        // Risiko-Metriken (Cash-Anteil, Beta-Wert)
        $depotInfo["risk_metrics"] = $this->calculateRiskMetrics($user, $depotInfo['totalPortfolioValue']);

        // Daten für den Dividenden-Chart
        $depotInfo["dividend_chart"] = 0;

        // Kaufkraft-Metrik
        $depotInfo["purchasing_power"] = $this->calculatePurchasingPower($stocks);



        // Testausgabe
        #dd($depotInfo);

        // Chart-Daten (Ingame-Monate) - Lazy Loading: nur laden wenn explizit angefordert
        $depotInfo['chartData'] = $this->createChartData($stocks, $user);

        return view('dashboard', compact('depotInfo'));
    }

    /**
     * Resolve a price value for a given stock and month with fallbacks.
     * Order of preference:
     *  - Price with exact same GameTime month
     *  - Latest price <= month
     *  - Earliest price > month
     *  - Transaction.price_at_buy (if transaction provided)
     *  - Current stock price
     *
     * @param int $stockId
     * @param \Carbon\Carbon $monthDate
     * @param \Illuminate\Support\Collection $pricesByStock grouped by stock_id
     * @param Transaction|null $tx optional transaction for using price_at_buy as fallback
     * @return float
     */
    private function resolvePriceForStockMonth(int $stockId, Carbon $monthDate, $pricesByStock, $tx = null): float
    {
        $prices = collect($pricesByStock->get($stockId) ?? []);

        // 1) exact same gameTime month
        $exact = $prices->first(function ($p) use ($monthDate) {
            if (isset($p->gameTime) && $p->gameTime) {
                $d = Carbon::parse($p->gameTime->name ?? now()->toDateString());
                return $d->eq($monthDate);
            }
            return false;
        });
        if ($exact) {
            $raw = $exact->name ?? 0;
            return is_string($raw) ? floatval(str_replace(',', '.', $raw)) : (float)$raw;
        }

        // 2) latest price <= month
        $latestBefore = $prices->filter(function ($p) use ($monthDate) {
            if (isset($p->gameTime) && $p->gameTime) {
                $d = Carbon::parse($p->gameTime->name ?? now()->toDateString());
                return $d->lte($monthDate);
            }
            return Carbon::parse($p->created_at ?? now())->startOfMonth()->lte($monthDate);
        })->last();
        if ($latestBefore) {
            $raw = $latestBefore->name ?? 0;
            return is_string($raw) ? floatval(str_replace(',', '.', $raw)) : (float)$raw;
        }

        // 3) earliest price > month
        $earliestAfter = $prices->filter(function ($p) use ($monthDate) {
            if (isset($p->gameTime) && $p->gameTime) {
                $d = Carbon::parse($p->gameTime->name ?? now()->toDateString());
                return $d->gt($monthDate);
            }
            return Carbon::parse($p->created_at ?? now())->startOfMonth()->gt($monthDate);
        })->first();
        if ($earliestAfter) {
            $raw = $earliestAfter->name ?? 0;
            return is_string($raw) ? floatval(str_replace(',', '.', $raw)) : (float)$raw;
        }

        // 4) transaction price_at_buy if provided
        if ($tx && isset($tx->price_at_buy) && $tx->price_at_buy !== null) {
            return (float)$tx->price_at_buy;
        }

        // 5) fallback to current stock price
        $stock = Stock::find($stockId);
        if ($stock) {
            return (float) $stock->getCurrentPrice();
        }

        return 0.0;
    }

    /**
     * Berechnet den Depotwert pro simuliertem Monat (Ingame-Zeit)
     */
    /**
     * Berechnet den Depotwert pro simuliertem Monat (Ingame-Zeit) nur basierend auf Aktien (ohne Cash)
     *
     * @param \Illuminate\Support\Collection $simulatedMonths Collection von Carbon-Monatsstarts (aufsteigend)
     * @return array
     */
    private function getHistoricalPortfolioValues($user, $simulatedMonths)
    {
        $values = [];

        // load all buy/sell transactions for the user
        $transactions = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['buy', 'sell'])
            ->orderBy('created_at')
            ->get();

        if ($transactions->isEmpty() || $simulatedMonths->isEmpty()) {
            return $simulatedMonths->map(fn() => 0.0)->all();
        }

        $stockIds = $transactions->pluck('stock_id')->unique()->filter()->all();

        // load prices grouped by stock
        $pricesByStock = Price::whereIn('stock_id', $stockIds)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('stock_id');

        // For each simulated month compute holdings and value
        foreach ($simulatedMonths as $monthDate) {
            $portfolioValue = 0.0;

            foreach ($stockIds as $stockId) {
                // compute holdings up to and including this month
                $holdings = $transactions
                    ->where('stock_id', $stockId)
                    ->reduce(function ($carry, $tx) use ($monthDate) {
                        // determine tx month (prefer gameTime if present)
                        if (isset($tx->game_time_id) && $tx->game_time_id) {
                            $gt = GameTime::find($tx->game_time_id);
                            if ($gt) {
                                $txMonth = Carbon::parse($gt->name ?? now()->toDateString());
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

                // Für historische Berechnungen alle Aktien berücksichtigen (auch wenn aktuell 0)
                // Der User möchte historische Werte sehen, auch von Aktien die er verkauft hat

                // resolve price for this stock and month with sensible fallbacks
                $priceValue = $this->resolvePriceForStockMonth($stockId, $monthDate, $pricesByStock);
                if ($priceValue > 0) {
                    $portfolioValue += $holdings * $priceValue;
                }
            }

            $values[] = round($portfolioValue, 2);
        }

        return $values;
    }

    /**
     * Berechnet die Portfolio-Performance für 3 und 6 Monate basierend auf GameTime.
     */
    private function calculatePortfolioPerformance($stocks, $user)
    {
        $gtService = new GameTimeService();
        $latestGameTime = GameTime::orderBy('created_at', 'desc')->first();
        if (!$latestGameTime) {
            return [
                "3_month" => ["amount" => 0, "percent" => 0],
                "6_month" => ["amount" => 0, "percent" => 0],
                "benchmark_ytd_percent" => 5.20,
                "benchmark_name" => "MSCI World",
            ];
        }

        $endMonth = $gtService->toDate($latestGameTime)->startOfMonth();
        $allGameTimes = GameTime::orderBy('created_at', 'asc')->get()->map(fn($gt) => $gtService->toDate($gt)->startOfMonth());

        // Letzte 6 Monate
        $simulatedMonths6 = $allGameTimes->reverse()->take(6)->reverse()->values();
        $portfolioValues6 = $this->getHistoricalPortfolioValues($user, $simulatedMonths6);
        $startValue6 = $portfolioValues6[0] ?? 0;
        $endValue6 = end($portfolioValues6) ?: 0;
        $amount6 = $endValue6 - $startValue6;
        $percent6 = $startValue6 > 0 ? ($amount6 / $startValue6) * 100 : 0;

        // Letzte 3 Monate
        $simulatedMonths3 = $allGameTimes->reverse()->take(3)->reverse()->values();
        $portfolioValues3 = $this->getHistoricalPortfolioValues($user, $simulatedMonths3);
        $startValue3 = $portfolioValues3[0] ?? 0;
        $endValue3 = end($portfolioValues3) ?: 0;
        $amount3 = $endValue3 - $startValue3;
        $percent3 = $startValue3 > 0 ? ($amount3 / $startValue3) * 100 : 0;

        return [
            "3_month" => ["amount" => round($amount3, 2), "percent" => round($percent3, 2)],
            "6_month" => ["amount" => round($amount6, 2), "percent" => round($percent6, 2)],
            "benchmark_ytd_percent" => 5.20,
            "benchmark_name" => "MSCI World",
        ];
    }

    /**
     * Berechnet Risiko-Kennzahlen: Cash, Investitionsquote, Beta.
     */
    private function calculateRiskMetrics($user, $totalPortfolioValue)
    {
        $cashBalance = $user->bank?->balance ?? 0;
        $totalCapital = $totalPortfolioValue;
        $investmentPercent = $totalCapital > 0 ? (($totalCapital - $cashBalance) / $totalCapital) * 100 : 0;

        return [
            "cash_balance" => $cashBalance,
            "total_capital" => $totalCapital,
            "portfolio_beta" => 1.15, // Hardcoded, da keine historischen Benchmark-Daten
        ];
    }

    /**
     * Berechnet Kaufkraft basierend auf jährlichen Dividenden und Beispiel-Aktie (Apple).
     */
    private function calculatePurchasingPower($stocks)
    {
        $annualDividends = 0.0;
        foreach ($stocks as $stockItem) {
            $stock = $stockItem->stock;
            if (!$stock) continue;
            $quantity = $stockItem->quantity ?? 0;
            $latestDividend = $stock->getLatestDividend();
            if ($latestDividend) {
                $annualDividends += ($latestDividend->amount_per_share ?? 0) * $quantity;
            }
        }

        // Beispiel-Aktie: Apple (AAPL), falls vorhanden, sonst erste Aktie
        $exampleStock = Stock::where('name', 'like', '%Apple%')->orWhere('name', 'like', '%AAPL%')->first();
        if (!$exampleStock) {
            $exampleStock = Stock::first();
        }
        $stockName = $exampleStock ? $exampleStock->name : 'N/A';
        $stockPrice = $exampleStock ? $exampleStock->getCurrentPrice() : 0;
        $canBuyQuantity = $stockPrice > 0 ? $annualDividends / $stockPrice : 0;

        return [
            "stock_name" => $stockName,
            "stock_price" => $stockPrice,
            "annual_gross_dividend" => round($annualDividends, 2),
            "can_buy_quantity" => round($canBuyQuantity, 2),
        ];
    }

    /**
     * Erzeugt Chart-Daten für Blade / Chart.js
     */
    private function createChartData($stocks, $user)
    {
        $months = session('timelineSelectedMonth', 12);

        // determine stock ids from provided $stocks collection (structure from StockService)
        $stockIds = collect($stocks)->map(fn($item) => data_get($item, 'stock.id'))->filter()->unique()->all();

        // Prefer the latest GameTime present in DB (created by time-skip). This ensures charts end at the
        // current ingame month (we orient exclusively on in-game time for the dashboard).
        $gtService = new GameTimeService();
        $latestGameTime = GameTime::orderBy('created_at', 'desc')->first();
        if (!$latestGameTime) {
            // Fallback if no GameTime exists
            return [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Depotwert (Ingame)',
                        'data' => [],
                        'borderColor' => '#10B981',
                        'backgroundColor' => 'rgba(16,185,129,0.1)',
                        'tension' => 0.3,
                        'fill' => true,
                        'pointRadius' => 4,
                        'borderWidth' => 2,
                    ],
                ],
            ];
        }

        $endMonth = $gtService->toDate($latestGameTime)->startOfMonth();

        // Also determine the earliest GameTime we have so we can avoid building months before game start
        $firstGameTime = GameTime::orderBy('created_at', 'asc')->first();
        $firstGameTimeMonth = $firstGameTime ? $gtService->toDate($firstGameTime)->startOfMonth() : null;

        // Build simulated months ending at $endMonth (ascending). Stop early if we reach the first known GameTime.
        // Only use GameTime months, not calendar months
        $allGameTimes = GameTime::orderBy('created_at', 'asc')->get()->map(fn($gt) => $gtService->toDate($gt)->startOfMonth());

        // Filter to get the most recent $months GameTime months
        $simulatedMonths = $allGameTimes->reverse()->take($months)->reverse()->values();

        // labels use month name + two-digit year
        $labels = $simulatedMonths->map(fn($d) => $d->format('F y'))->all();

        $portfolioValues = $this->getHistoricalPortfolioValues($user, $simulatedMonths);

        // compute monthly P&L as difference between months (month i minus month i-1)
        $monthlyPnl = [];
        for ($i = 0; $i < count($portfolioValues); $i++) {
            if ($i === 0) {
                $monthlyPnl[] = round($portfolioValues[0], 2);
            } else {
                $monthlyPnl[] = round($portfolioValues[$i] - $portfolioValues[$i - 1], 2);
            }
        }

        // compute monthly net investments: sum of (qty * month_price) for transactions occurring in that month
        $transactions = Transaction::where('user_id', $user->id)->whereIn('type', ['buy', 'sell'])->get();
        $pricesByStock = Price::whereIn('stock_id', $stockIds)->orderBy('created_at', 'asc')->get()->groupBy('stock_id');

        $monthlyInvest = [];
        $priceResolver = new PriceResolverService();
        foreach ($simulatedMonths as $monthDate) {
            $sum = 0.0;
            foreach ($transactions as $tx) {
                // D) Strict game_time-based matching: only count transactions that have a game_time_id
                // and whose GameTime month equals the simulated month. This makes monthlyInvest purely in-game.
                if (!isset($tx->game_time_id) || !$tx->game_time_id) continue;
                $gt = GameTime::find($tx->game_time_id);
                if (!$gt) continue;
                $txMonth = Carbon::parse($gt->name ?? now()->toDateString());
                if (!$txMonth->eq($monthDate)) continue;

                // resolve month price for tx stock (use resolver service; tx used as fallback)
                $priceVal = $priceResolver->resolvePriceForStockMonth($tx->stock_id, $monthDate, $pricesByStock, $tx);

                $qty = $tx->quantity * ($tx->type === 'buy' ? 1 : -1);
                $sum += $qty * $priceVal;
            }
            $monthlyInvest[] = round($sum, 2);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Depotwert (Ingame)',
                    'data' => $portfolioValues,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16,185,129,0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                    'pointRadius' => 4,
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Monatlicher Gewinn (P/L)',
                    'data' => $monthlyPnl,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59,130,246,0.08)',
                    'tension' => 0.2,
                    'fill' => false,
                    'pointRadius' => 3,
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Monatliche Investition (Netto)',
                    'data' => $monthlyInvest,
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245,158,11,0.08)',
                    'tension' => 0.2,
                    'fill' => false,
                    'pointRadius' => 3,
                    'borderWidth' => 2,
                ],
            ],
        ];
    }
}
