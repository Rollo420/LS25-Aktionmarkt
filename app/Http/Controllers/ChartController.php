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

        //foreach($stocks as $stock)
        //{
        //   
        //    //dd($stocks);
        //}

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
                ]
            ];
        }

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
                $stockData[] = 
                [
                    'label' => $product->name,
                    'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,                    
                    'data' => Product_type::find(1)->stock()->getRelation('price')->get()->pluck('name')->toArray(),
                ];


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
        return $listStock ;

    }


}
