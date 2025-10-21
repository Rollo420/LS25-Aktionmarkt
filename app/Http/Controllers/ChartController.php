<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Log\Context;

//My Controller
use Colors\RandomColor;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\StockController;

//My Models
use App\Models\Stock\Price;
use App\Models\Stock\Stock;

class ChartController extends Controller
{
    /**
     * Generiert eine zufällige RGBA-Farbe für die Darstellung in Diagrammen.
     *
     * @param float $alpha Transparenzwert (Standard: 1.0)
     * @return string RGBA-Farbstring
     */
    private function randomRGBA($alpha = 1.0)
    {
        $colors = [
            [255, rand(0, 100), rand(0, 100)],
            [rand(0, 100), 255, rand(0, 100)],
            [rand(0, 100), rand(0, 100), 255],
            [255, 255, rand(0, 100)],
            [255, rand(0, 100), 255],
            [rand(0, 100), 255, 255],
        ];

        $rgb = $colors[array_rand($colors)];
        return sprintf('rgba(%d, %d, %d, %.1f)', $rgb[0], $rgb[1], $rgb[2], $alpha);
    }

    /**
     * Erstellt die Chart-Daten für alle Aktienpreise.
     *
     * @return void
     */
    public function CreateAllChartData(): void
    {
        $prices = Price::all();
        $listStock = [
            'labels' => [],
            'datasets' => []
        ];

        foreach ($prices as $price) {
            $color = $this->randomRGBA(0.2);

            $listStock['labels'][] = $price->month . ' ' . $price->year;
            $listStock['datasets'][] = [
                'label' => $price->stock->name,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'data' => [$price->name],
                'fill' => false,
            ];
        }

        if (empty($listStock['datasets'])) {
            $listStock['datasets'][] = [
                'label' => 'Error Test Chart',
                'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1,
                'data' => [65, 59, 80, 81, 56, 55, 40],
                'fill' => false,
            ];
        }

        // Removed recursive call to avoid infinite recursion
        // Pass $listStock to the view or handle it as needed
    }

    /**
     * Zeigt ein einzelnes Chart für eine Aktie an.
     *
     * @param int $id Die ID der Aktie
     * @param int $limit Anzahl der anzuzeigenden Monate (Standard: 12 oder aus Session)
     * @return \Illuminate\View\View
     */
    public function OneChart($id, $limit = 12)
    {
        // Hole den Wert aus der Session oder setze einen Standardwert (z. B. 12)
        $limit = session('timelineSelectedMonth', 12) ?? $limit;

        $stock = Stock::findOrFail($id);
        $color = $this->randomRGBA(0.2);
        //dd($stock);
        // Order primarily by linked game_time (if present), otherwise by created_at
        $prices = Price::where('stock_id', $id)
            ->orderByDesc('game_time_id')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();

        $sortedPrices = $prices->sortByDesc(function ($price) {
            if (isset($price->gameTime) && $price->gameTime) {
                return ($price->gameTime->current_year ?? 0) * 100 + ($price->gameTime->month_id ?? 0);
            }
            return strtotime($price->created_at ?? 0);
        })->values();


        $listStock = [
            'labels' => $sortedPrices->map(function ($price) {
                if (isset($price->gameTime) && $price->gameTime) {
                    $monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                    $m = $price->gameTime->month_id ?? 1;
                    $yr = $price->gameTime->current_year ?? date('Y');
                    return ($monthNames[$m - 1] ?? 'Month') . ' ' . substr($yr, -2);
                }
                return isset($price->created_at) ? date('F y', strtotime($price->created_at)) : 'Unknown Date';
            })->toArray(),
            'datasets' => [[
                'label' => $stock->name,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'data' => $sortedPrices->map(function ($price) {
                    return $price->name ?? 0; // Fallback auf 0, wenn 'name' fehlt
                })->toArray(),
                'fill' => false,
            ]]
        ];

        // Fallback-Daten, wenn keine gültigen Daten vorhanden sind
        if (empty($listStock['datasets'][0]['data'])) {
            $listStock['datasets'] = [[
                'label' => 'Error Test Chart',
                'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1,
                'data' => [65, 59, 80, 81, 56, 55, 40],
                'fill' => false,
            ]];
        }

        $chartOptions = [
            'scales' => [
                'y' => [
                    'beginAtZero' => true
                ]
            ]
        ];

        $stockController = new StockController();
        $stockDetails = $stockController->stockDetails($id);
        //dd($id);
        return view('Stock.store', [
            'chartData' => $listStock,
            'chartOptions' => $chartOptions,
            'stock' => $stock,
            'details' => $stockDetails
        ]);
    }
}
