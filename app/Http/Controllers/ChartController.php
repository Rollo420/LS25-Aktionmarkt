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

    public function OneChart($id)
    {
        $stock = Stock::findOrFail($id);
        $color = $this->randomRGBA(0.2);

        // Ensure the 'date' field exists in the price model
        $sortedPrices = $stock->price->sortBy(function ($price) {
            return isset($price->date) ? strtotime($price->date) : 0; // Fallback to 0 if 'date' is missing
        });

        $listStock = [
            'labels' => $sortedPrices->map(function ($price) {
                return isset($price->date) ? date('F Y', strtotime($price->date)) : 'Unknown Date';
            })->toArray(),
            'datasets' => [[
                'label' => $stock->name,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'data' => $sortedPrices->map(function ($price) {
                    return $price->name ?? 0; // Fallback to 0 if 'name' is missing
                })->toArray(),
                'fill' => false,
            ]]
        ];

        // Replace datasets with fallback data if no valid data exists
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
        $stockDetails = $stockController->details($id);

        return view('Stock.store', [
            'chartData' => $listStock,
            'chartOptions' => $chartOptions,
            'stock' => $stockDetails
        ]);
    }

    public function stockDetail($id)
    {
        return Stock::findOrFail($id);
    }
}
