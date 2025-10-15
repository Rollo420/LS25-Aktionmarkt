<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\StockService;
use App\Services\DividendeService;

use App\Models\Stock\Transaction;



class DashboardController extends Controller
{

    public function index(StockService $stockService, DividendeService $dividendeService)
    {
        $depotInfo = [];

        $user = Auth::user();
        $stocks = $stockService->getUserStocksWithStatistiks($user);

        $depotInfo['totalPortfolioValue'] = $stockService->getTotalPortfolioValue();

        $depotInfo['tops'] = [
            'topThreeUp' => $stocks->take(3)->values()->map( function ($item) use ($stockService) {
                return $stockService->getStockStatistiks($item->stock, Auth::user());
            })->toArray(),
            'topThreeDown' => $stocks->slice(3)->sortBy('profit_loss.amount')
                ->take(3)->values()->map(function ($item) use ($stockService) {
                    return $stockService->getStockStatistiks($item->stock, Auth::user());
            })->toArray(),
        ];

        $depotInfo['lastTransactions'] = Transaction::where('user_id', $user->id)
            ->with('stock')
            ->latest()
            ->take(5)
            ->get()
            ->toArray();

        $depotInfo['nextDividens'] = collect(        
            $stocks
                ->sortByDesc(function ($item) {
                    return $item->stock->getDividendenDate();
                })
                ->map(function ($item) {
                    return [
                        'name' => $item->stock->name,
                        'next_dividend' => $item->stock->getDividendenDate(),
                    ];
                })
                ->values()
                ->toArray()
        )->take(5)->toArray();

        $depotInfo['avg_dividend'] = $stockService
            ->getUserStocks($user)
            ->map(fn($stock) => $dividendeService->getDividendeForStock($stock->id))
            ->avg();


        $depotInfo['chartData'] = $this->createChartData($stocks);

        #dd($depotInfo);

        return view('dashboard', compact('depotInfo'));
    }

    private function getHistoricalPortfolioValues($user, $months = 12)
    {
        $values = [];
        $currentDate = now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i)->endOfMonth();

            // Get all transactions up to this date
            $transactions = Transaction::where('user_id', $user->id)
                ->whereIn('type', ['buy', 'sell'])
                ->where('created_at', '<=', $date)
                ->orderBy('created_at')
                ->get();

            $portfolioValue = 0;
            $holdings = [];

            // Calculate holdings by processing transactions chronologically
            foreach ($transactions as $transaction) {
                $stockId = $transaction->stock_id;
                if (!isset($holdings[$stockId])) {
                    $holdings[$stockId] = 0;
                }

                if ($transaction->type === 'buy') {
                    $holdings[$stockId] += $transaction->quantity;
                } elseif ($transaction->type === 'sell') {
                    $holdings[$stockId] -= $transaction->quantity;
                }
            }

            // Calculate portfolio value based on current holdings
            foreach ($holdings as $stockId => $quantity) {
                if ($quantity > 0) {
                    // Get the last price for this stock up to the date
                    $lastPrice = \App\Models\Stock\Price::where('stock_id', $stockId)
                        ->where('created_at', '<=', $date)
                        ->latest('created_at')
                        ->first();

                    if ($lastPrice) {
                        $portfolioValue += $quantity * $lastPrice->name;
                    }
                }
            }

            // Add bank balance (assuming current balance, as historical bank data may not be available)
            #$portfolioValue += $user->bank->balance;

            $values[] = round($portfolioValue, 2);
        }

        return $values;
    }

    private function createChartData($stocks)
    {
        $user = Auth::user();
        $labels = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];

        // Get historical portfolio values
        $data = $this->getHistoricalPortfolioValues($user, 12);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Depotentwicklung 2025',
                    'data' => $data,
                    'borderColor' => '#10B981', // Grün (Tailwind Emerald)
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
