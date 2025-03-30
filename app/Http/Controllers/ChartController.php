<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Log\Context;

//My Controller
use Colors\RandomColor;
use App\Http\Controllers\TimeController;

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

        $this->CreateAllChartData($listStock);
    }

    public function OneChart($id)
{
    $stock = Stock::findOrFail($id);
    $color = $this->randomRGBA(0.2);

    // Sortiere die Preise nach Datum (von Januar nach Dezember)
    $sortedPrices = $stock->price->sortBy(function ($price) {
        return strtotime($price->date); // Sortiere nach dem Date-Wert
    });

    // Bereite die Labels (Monat Jahr) und die Daten vor
    $listStock = [
        'labels' => $sortedPrices->map(function ($price) {
            return date('F Y', strtotime($price->date)); // Monat Jahr (z.B. Januar 2023)
        })->toArray(),
        'datasets' => [[
            'label' => $stock->name,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'data' => $sortedPrices->map(function ($price) {
                return $price->name; // Hier kannst du den Preiswert oder einen anderen Wert verwenden
            })->toArray(),
            'fill' => false,
        ]]
    ];

    // Fallback-Daten, wenn keine Daten vorhanden sind
    if (empty($listStock['datasets'][0]['data'])) {
        $listStock['datasets'][] = [
            'label' => 'Error Test Chart',
            'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'borderWidth' => 1,
            'data' => [65, 59, 80, 81, 56, 55, 40], // Beispielwerte
            'fill' => false,
        ];
    }

    // Chart-Optionen
    $chartOptions = [
        'scales' => [
            'y' => [
                'beginAtZero' => true
            ]
        ]
    ];

    // Rückgabe der Daten an die View
    return view('Stock.store', ['chartData' => $listStock, 'chartOptions' => $chartOptions]);
}




}
