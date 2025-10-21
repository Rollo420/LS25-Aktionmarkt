<?php

namespace App\Http\Controllers;

use App\Models\BuyTransaction;
use App\Models\Stock\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\StockService;
use App\Services\DividendeService;
use Carbon\Carbon;

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
                    'next_dividend' => $divData['dividende']['nextDividendDate'],
                    'price' => $stock->getLatestPrice(),                        // aktueller Kurs (€)
                    'dividend' => $divData['dividende']['dividendPerShare'], // Dividende (€)
                    'percent' => $divData['dividende']['dividendPercent'],    // Rendite (%)
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
     * Berechnet den Depotwert pro simuliertem Monat (Ingame-Zeit)
     */
    private function getHistoricalPortfolioValues($user, $months = 12)
    {
        $values = [];

        $transactions = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['buy', 'sell'])
            ->orderBy('created_at')
            ->get();

        if ($transactions->isEmpty()) {
            return array_fill(0, $months, 0);
        }

        $stockIds = $transactions->pluck('stock_id')->unique()->all();

            // group prices by stock and by associated game_time (month/year)
            $prices = \App\Models\Stock\Price::whereIn('stock_id', $stockIds)
                ->orderBy('game_time_id', 'asc')
                ->orderBy('created_at', 'asc')
                ->get()
                ->groupBy('stock_id');

            // collect unique GameTime month/year combos (use year-month key to dedupe)
            $dateMap = [];
            foreach ($prices->flatten() as $p) {
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
            $allDates = collect(array_values($dateMap))->sortBy(fn($d) => $d->timestamp)->values();

            $simulatedMonths = $allDates->slice(-$months);

        $holdingsByStock = [];
        foreach ($stockIds as $stockId) {
            $holdingsByStock[$stockId] = 0;
        }

        foreach ($simulatedMonths as $monthDate) {
            $portfolioValue = 0;

            foreach ($stockIds as $stockId) {
                $holdingsByStock[$stockId] = $transactions
                    ->where('stock_id', $stockId)
                    ->filter(function ($tx) use ($monthDate, $allDates) {
                        $txCreated = Carbon::parse($tx->created_at);
                        $txMonth = $allDates->first(fn($d) => $d->gte($txCreated));
                        return $txMonth && $txMonth->lte($monthDate);
                    })
                    ->reduce(fn($carry, $tx) => $carry + ($tx->type === 'buy' ? $tx->quantity : -$tx->quantity), $holdingsByStock[$stockId]);

                if ($holdingsByStock[$stockId] <= 0)
                    continue;

                $price = optional($prices->get($stockId))
                    ->filter(function($p) use ($monthDate) {
                        if (isset($p->gameTime) && $p->gameTime) {
                            $d = Carbon::createFromDate($p->gameTime->current_year ?? date('Y'), $p->gameTime->month_id ?? 1, 1);
                            return $d->lte($monthDate);
                        }
                        // fallback to created_at if no GameTime present
                        return Carbon::parse($p->created_at ?? now())->lte($monthDate);
                    })
                    ->last();

                if ($price && $price->name > 0) {
                    $portfolioValue += $holdingsByStock[$stockId] * $price->name;
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

            // Build global unique month/year list from all prices (dedupe by year-month key)
            $dateMap = [];
            foreach (\App\Models\Stock\Price::orderBy('game_time_id', 'asc')->get() as $p) {
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
            $allDates = collect(array_values($dateMap))->sortBy(fn($d) => $d->timestamp)->values();

            $simulatedMonths = $allDates->slice(-$months);

            $labels = $simulatedMonths->map(fn($d) => $d->format('M Y'))->all();

        $data = $this->getHistoricalPortfolioValues($user, $months);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Depotentwicklung (Ingame)',
                    'data' => $data,
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
}
