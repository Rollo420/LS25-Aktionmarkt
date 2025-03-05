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
<<<<<<< HEAD
        
     
=======
>>>>>>> 01d87fc59cb0a3b86a7f0a15b97c9fbacb85dcf7
        $stocks = $this->CreateChartData();

       
        $chartData = [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'datasets' => $stocks
        ];

<<<<<<< HEAD
        $chartOptions = [
            'scales' => [
                'y' => [
                    'beginAtZero' => true
=======
        $stocks[] = [
            [
                'label' => 'Test01',
                'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1,
                'data' => [65, 59, 80, 81, 56, 55, 40],
                'fill' => false,
            ],
            [   'label' => 'Test02',
                'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1,
                'data' => [28, 48, 40, 19, 86, 27, 90],
                'fill' => false,
            ]
        ];
        
        
        
        foreach ($stocks as $stock) {
            foreach ($stock as $item) {
                $chartData = [
                    'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    'datasets' => $stocks[0]
                ];
            }
            $chartOptions = [
                'scales' => [
                    'y' => [
                        'beginAtZero' => true
                    ]
>>>>>>> 01d87fc59cb0a3b86a7f0a15b97c9fbacb85dcf7
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
<<<<<<< HEAD
            {               
                $stockData = 
=======
            {                
                $stockData[] = 
>>>>>>> 01d87fc59cb0a3b86a7f0a15b97c9fbacb85dcf7
                [
                    'label' => $product->name,
                    'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,                    
<<<<<<< HEAD
                    'data' => $product->stock->map(function($stock) {
                        return $stock->price->name;
                    })->toArray(),
                ];

=======
                    'data' => Product_type::find(1)->stock()->getRelation('price')->get()->pluck('name')->toArray(),
                ];


>>>>>>> 01d87fc59cb0a3b86a7f0a15b97c9fbacb85dcf7
                array_push($listStock, $stockData);
            }            
            dd($stockData);

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
