<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function show()
    {
        $chartData = [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgba(32, 229, 18, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                    'data' => [65, 59, 80, 81, 56, 55, 40],
                    'fill' => false,
                ],
                // Weitere Datensätze können hier hinzugefügt werden
            ]
        ];

        $chartOptions = [
            'scales' => [
                'y' => [
                    'beginAtZero' => true
                ]
            ]
        ];

        return view('example', compact('chartData', 'chartOptions'));
    }
}
