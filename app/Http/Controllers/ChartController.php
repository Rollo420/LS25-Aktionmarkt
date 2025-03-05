<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Log\Context;

//My Models
use App\Models\Stock\Product_type;

class ChartController extends Controller
{
    public function show()
    {
        
     
        $stocks = $this->CreateChartData();

       
        $chartData = [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'datasets' => $stocks
        ];

        $chartOptions = [
            'scales' => [
                'y' => [
                    'beginAtZero' => true
                ]
            ]
        ];
        

        return view('chart', ['data' => '', 'chartData' => $chartData, 'chartOptions' => $chartOptions]);//compact('chartData', 'chartOptions', 'data'));
    }

    

    public function CreateChartData(): array
    {
        $listStock = [];

        $productChart = Product_type::all();
        if ($productChart) 
        {
            foreach ($productChart as $product) 
            {               
                $stockData = 
                [
                    'label' => $product->name,
                    'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,                    
                    'data' => $product->stock->map(function($stock) {
                        return $stock->price->name;
                    })->toArray(),
                ];

                array_push($listStock, $stockData);
            }            
        } else {
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

        // Debugging: Überprüfen Sie die abgerufenen Daten
        //dd($listStock);

        return $listStock;
    }
}
