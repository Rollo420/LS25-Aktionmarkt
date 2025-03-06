<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Log\Context;

//My Controller
use Colors\RandomColor;
use App\Http\Controllers\TimeController;

//My Models
use App\Models\Stock\Product_type;


class ChartController extends Controller
{
    public function show()
    {
        $timeController = new TimeController;
        $stocks = $this->CreateChartData();
        $timeController->mainTime();
        $chartData = [
            'labels' => $timeController->monthArray,
            'datasets' => $stocks
        ];

        $chartOptions = [
            'scales' => [
                'y' => [
                    'beginAtZero' => true
                ]
            ]
        ];

        return view('chart', [ 'chartData' => $chartData, 'chartOptions' => $chartOptions]);
    }

    function randomRGBA($alpha = 1.0)
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
    public function CreateChartData(): array
    {
        $listStock = [];

        $productChart = Product_type::all();
        foreach ($productChart as $product) {

            $color = $this->randomRGBA(0.2);
            $listStock[] = [
                'label' => $product->name,
                'backgroundColor' => $color,
                'borderColor' => $color,
                //'borderWidth' => 1,
                'data' => $product->stock->map(function ($stock) {
                    return $stock->price->name;
                })->toArray(),
                'fill' => false,
            ];
        }

        if (empty($listStock)) {
            $listStock = [
                [
                    'label' => 'Error Test Chart',
                    'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                    'data' => [65, 59, 80, 81, 56, 55, 40],
                    'fill' => false,
                ],
            ];
        }

        return $listStock;
    }
}
