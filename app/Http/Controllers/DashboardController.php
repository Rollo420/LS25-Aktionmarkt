<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Top/Flop Aktien
        $depotInfo['tops'] = [
            'topThreeUp' => $stocks->take(3)->values()->map(function (object $item) use ($stockService) {
                #dd($item);
                return $stockService->getStockStatistiks($item->stock, Auth::user());
            })->toArray(),
            'topThreeDown' => $stocks->slice(3)->sortBy('profit_loss.amount')
                ->take(limit: 3)->values()->map(function ($item) use ($stockService) {
                    return $stockService->getStockStatistiks($item->stock, Auth::user());
                })->toArray(),
        ];

        // Letzte 5 Transaktionen
        $depotInfo['lastTransactions'] = Transaction::where('user_id', $user->id)
            ->with('stock')
            ->latest()
            ->take(5)
            ->get()
            ->toArray();


        $dividendeService = new DividendeService();

        $depotInfo['nextDividends'] = collect($stocks)
            ->sortByDesc(fn($item) => $item->stock->getNextDividendDate())
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


        // --- HINZUFÜGEN DER NEUEN DUMMY-DATEN ---

        // Performance-Metriken (3-Monats, 6-Monats & Benchmark)
        $depotInfo["monthly_performance"] = [
            "3_month" => [
                "amount" => 150.50, // Betrag der 3-Monats-Performance
                "percent" => 1.24,  // Prozent der 3-Monats-Performance
            ],
            "6_month" => [
                "amount" => 450.00,
                "percent" => 3.71,
            ],
            "benchmark_ytd_percent" => 5.20, // Benchmark-Performance (für 1.3)
            "benchmark_name" => "MSCI World", // Benchmark-Name (für 1.3)
        ];

        // Risiko-Metriken (Cash-Anteil, Beta-Wert)
        $depotInfo["risk_metrics"] = [
            "cash_balance" => 3500.00, // Beispiel Cash-Guthaben
            "total_capital" => $depotInfo['totalPortfolioValue'] + 3500.00,
            "portfolio_beta" => 1.15, // Beispiel Beta-Wert
        ];

        // Daten für den Dividenden-Chart
        $depotInfo["dividend_chart"] = [
            "labels" => ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
            "data" => [30, 45, 60, 35, 70, 50, 40, 55, 65, 80, 55, 60], // Erwarteter Betrag pro Monat
        ];

        // Kaufkraft-Metrik
        $depotInfo["purchasing_power"] = [
            "stock_name" => "Apple (AAPL)",
            "stock_price" => 170.00,
            "annual_gross_dividend" => 305.00, // Summe aller erwarteten Dividenden
            "can_buy_quantity" => 305.00 / 170.00, // Dummy-Berechnung
        ];

        // --- Ende der neuen Dummy-Daten ---



        // Testausgabe
        #dd($depotInfo);

        // Chart-Daten (Ingame-Monate)
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

                if ($holdings <= 0) {
                    continue;
                }

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
     * Erzeugt Chart-Daten für Blade / Chart.js
     */
    private function createChartData($stocks, $user)
    {
        $months = 12;

        // determine stock ids from provided $stocks collection (structure from StockService)
        $stockIds = collect($stocks)->map(fn($item) => data_get($item, 'stock.id'))->filter()->unique()->all();

        // Build unique month/year list only from prices of the user's stocks
        $dateMap = [];
        $prices = Price::whereIn('stock_id', $stockIds)->orderBy('created_at', 'asc')->get();
        foreach ($prices as $p) {
            if (isset($p->gameTime) && $p->gameTime) {
                $date = Carbon::parse($p->gameTime->name ?? now()->toDateString());
                $year = $date->year;
                $month = $date->month;
            } else {
                $dt = Carbon::parse($p->created_at ?? now());
                $year = (int) $dt->format('Y');
                $month = (int) $dt->format('m');
            }
            $key = sprintf('%04d-%02d', $year, $month);
            $dateMap[$key] = Carbon::createFromDate($year, $month, 1);
        }

        // Also include months from the user's transactions (so months where user bought/sold are present)
        $txMonths = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['buy', 'sell'])
            ->get();
        foreach ($txMonths as $tx) {
            if (isset($tx->game_time_id) && $tx->game_time_id) {
                $gt = GameTime::find($tx->game_time_id);
                if ($gt) {
                    $date = Carbon::parse($gt->name ?? now()->toDateString());
                    $y = $date->year;
                    $m = $date->month;
                } else {
                    $dt = Carbon::parse($tx->created_at ?? now());
                    $y = (int)$dt->format('Y');
                    $m = (int)$dt->format('m');
                }
            } else {
                $dt = Carbon::parse($tx->created_at ?? now());
                $y = (int)$dt->format('Y');
                $m = (int)$dt->format('m');
            }
            $k = sprintf('%04d-%02d', $y, $m);
            $dateMap[$k] = Carbon::createFromDate($y, $m, 1);
        }

    // Determine an appropriate end month: prefer latest GameTime (so time-skip is reflected),
    // then latest price month, then latest transaction month, then now
        $latestPrice = Price::whereIn('stock_id', $stockIds)->orderBy('created_at', 'desc')->first();
        $latestPriceMonth = null;
        if ($latestPrice) {
            if (isset($latestPrice->gameTime) && $latestPrice->gameTime) {
                $gt = $latestPrice->gameTime;
                $latestPriceMonth = Carbon::parse($gt->name ?? now()->toDateString());
            } else {
                $latestPriceMonth = Carbon::parse($latestPrice->created_at ?? now())->startOfMonth();
            }
        }

        $latestTx = Transaction::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
        $latestTxMonth = null;
        if ($latestTx) {
            if (isset($latestTx->game_time_id) && $latestTx->game_time_id) {
                $gt = GameTime::find($latestTx->game_time_id);
                if ($gt) {
                    $latestTxMonth = Carbon::parse($gt->name ?? now()->toDateString());
                }
            }
            if (!$latestTxMonth) {
                $latestTxMonth = Carbon::parse($latestTx->created_at ?? now())->startOfMonth();
            }
        }

        $endMonth = collect([$latestPriceMonth, $latestTxMonth, Carbon::now()->startOfMonth()])->filter()->sortByDesc(fn($d) => $d->timestamp)->first();
        if (!$endMonth) $endMonth = Carbon::now()->startOfMonth();

        // Prefer the latest GameTime present in DB (created by time-skip). This ensures charts end at the
        // current ingame month (we orient exclusively on in-game time for the dashboard).
        $gtService = new GameTimeService();
        $latestGameTime = GameTime::orderBy('created_at', 'desc')->first();
        if ($latestGameTime) {
            $latestGameTimeMonth = $gtService->toDate($latestGameTime)->startOfMonth();
            // Use the latest created GameTime as the canonical current in-game month
            $endMonth = $latestGameTimeMonth;
        }

        // Also determine the earliest GameTime we have so we can avoid building months before game start
        $firstGameTime = GameTime::orderBy('created_at', 'asc')->first();
        $firstGameTimeMonth = $firstGameTime ? $gtService->toDate($firstGameTime)->startOfMonth() : null;

        // Build simulated months ending at $endMonth (ascending). Stop early if we reach the first known GameTime.
        $simulatedMonths = collect();
        for ($i = $months - 1; $i >= 0; $i--) {
            $candidate = $endMonth->copy()->subMonths($i);
            if ($firstGameTimeMonth && $candidate->lt($firstGameTimeMonth)) {
                // skip months before the first recorded GameTime — produce a shorter series instead
                continue;
            }
            $simulatedMonths->push($candidate);
        }

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
