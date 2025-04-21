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

    public function OneChart($id, $limit = 12)
    {
    // Hole den Wert aus der Session oder setze einen Standardwert (z. B. 12)
    $limit = session('timelineSelectedMonth', 12) ?? $limit;

        $stock = Stock::findOrFail($id);
        $color = $this->randomRGBA(0.2);
        //dd($stock);
        $prices = Price::where('stock_id', $id)->orderBy('date', 'desc')->take($limit)->get();

        $sortedPrices = $prices->sortBy(function ($price) {
            return isset($price->date) ? strtotime($price->date) : 0; // Fallback auf 0, wenn 'date' fehlt
        })->values();


        $listStock = [
            'labels' => $sortedPrices->map(function ($price) {
                return isset($price->date) ? date('F Y', strtotime($price->date)) : 'Unknown Date';
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
