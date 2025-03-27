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
            
            $lastDate = $stock->price()->get()->last()->date;
            $lastDate = date('Y-M-d', $lastDate);
            dd($lastDate);
            
        }


        //dd(['CurrentMonth' => ['idx' => $currentMonthIdx, 'name' => $currentMonth], 'lastMonth' => ['idx' => $lastMonthIdx, 'name' => $lastMonth->month, 'id' => $lastMonth->id]]);
    }

    public function getMonthIndex($month)
    {
        $formattedMonth = ucfirst(strtolower($month));
        return array_search($formattedMonth, $this->monthArray);
    }

    function getNextMonthDate($monthName) {
    $month = date("n", strtotime($monthName)); // Konvertiere Monatsname in Zahl
    $currentYear = date("Y");
    $currentMonth = date("n");

    // Falls der Monat schon vorbei ist, nehme das nächste Jahr
    $targetYear = ($currentMonth >= $month) ? $currentYear + 1 : $currentYear;

    return date("Y-m-d", strtotime("first day of $monthName $targetYear"));
}
}
