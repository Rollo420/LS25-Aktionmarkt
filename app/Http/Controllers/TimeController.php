<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Stock\Price;
use App\Models\Stock\Stock;

class TimeController extends Controller
{
    public $monthArray =  ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    public function index()
    {
        $insertData = [];
        $selectedMonth = session('selectedMonth', 'None');
        return view('time.index', ['monthArray' => $this->monthArray, 'selectedMonth' => $selectedMonth, 'insertData' => $insertData]);
    }

    public function update(Request $request)
    {
        $selectedMonth = $request->input('choose');
        $this->skipTime($selectedMonth);
        session(['selectedMonth' => $selectedMonth]);
        return redirect()->route('time.index');
    }

    public function skipTime($currentMonth)
    {
        $currentMonthIdx = $this->getMonthIndex($currentMonth);
        $stocks = Stock::all();

        //wichtig $stock->price()->get()->last()->month!!!
        foreach ($stocks as $stock) {
            $lastPrice = $stock->price()->get()->last();
            $lastMonth = $lastPrice->month;
            $lastMonthIdx = $this->getMonthIndex($lastMonth);
            $lastYear = $lastPrice->year;

            if ($currentMonthIdx < $lastMonthIdx) {
                foreach ($this->monthArray as $index => $value) {
                    // Wenn der Index das Ende des Arrays erreicht hat, gehe zurück zum Anfang
                    if ($index === count($this->monthArray) - 1) 
                    {
                        $index = -1; 
                        $lastYear++; 
                    }

                    $insertData = [
                        'name' => fake()->randomFloat(2, 0, 100), 
                        'month' => $value,
                        'stock_id' => $stock->id,
                        'year' => $lastYear,
                    ];
                    //dd($insertData);
                    // Führe das Einfügen in die Datenbank durch
                    Price::create($insertData);

                    //$stock = new Stock();                    
                    //$price = new Price();
//
                    //$price-> name = 10;
                    //$stock->price = [$price];
                    //$stock-> save();
                }
            }
        }


        //dd(['CurrentMonth' => ['idx' => $currentMonthIdx, 'name' => $currentMonth], 'lastMonth' => ['idx' => $lastMonthIdx, 'name' => $lastMonth->month, 'id' => $lastMonth->id]]);
    }

    public function getMonthIndex($month)
    {
        $formattedMonth = ucfirst(strtolower($month));
        return array_search($formattedMonth, $this->monthArray);
    }
}
