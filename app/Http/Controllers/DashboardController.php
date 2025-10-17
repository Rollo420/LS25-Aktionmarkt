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

        $depotInfo = [];

        // Gesamtwert des Portfolios
        $depotInfo['totalPortfolioValue'] = $stockService->getTotalPortfolioValue();

        // Top/Flop Aktien
        $depotInfo['tops'] = [
            'topThreeUp' => $stocks->take(3)->values()->map(fn($item) => $stockService->getStockStatistiks($item->stock, $user))->toArray(),
            'topThreeDown' => $stocks->slice(3)->sortBy('profit_loss.amount')->take(3)->values()->map(fn($item) => $stockService->getStockStatistiks($item->stock, $user))->toArray(),
        ];

        // Letzte 5 Transaktionen
        $depotInfo['lastTransactions'] = Transaction::where('user_id', $user->id)
            ->with('stock')
            ->latest()
            ->take(5)
            ->get()
            ->toArray();

        // Nächste Dividenden
        $depotInfo['nextDividens'] = collect(
            $stocks->sortByDesc(fn($item) => $item->stock->getDividendenDate())
                ->map(fn($item) => [
                    'name' => $item->stock->name,
                    'next_dividend' => $item->stock->getDividendenDate(),
                ])
        )->take(5)->toArray();

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

        $prices = \App\Models\Stock\Price::whereIn('stock_id', $stockIds)
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('stock_id');

        $allDates = $prices->flatten()
            ->pluck('date')
            ->unique()
            ->sort()
            ->map(fn($d) => Carbon::parse($d))
            ->values();

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
                    ->filter(fn($p) => Carbon::parse($p->date) <= $monthDate)
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

        $allDates = \App\Models\Stock\Price::orderBy('date', 'asc')
            ->pluck('date')
            ->unique()
            ->map(fn($d) => Carbon::parse($d))
            ->values();

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
