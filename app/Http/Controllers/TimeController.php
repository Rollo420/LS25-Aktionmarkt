<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Stock\Price;
use App\Models\Stock\Stock;

class TimeController extends Controller
{
    /**
     * Array mit Monatsnamen für die Zeitsimulation.
     *
     * @var array
     */
    public $monthArray =  ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    /**
     * Zeigt die Monatsauswahl und den aktuellen Monat an.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $insertData = [];
        $selectedMonth = session('selectedMonth', 'None');
        return view('time.index', ['monthArray' => $this->monthArray, 'selectedMonth' => $selectedMonth, 'insertData' => $insertData]);
    }

    /**
     * Setzt den gewählten Monat in der Session und ruft die Zeit-Sprung-Logik auf.
     *
     * @param \Illuminate\Http\Request $request HTTP-Request mit 'choose' als Parameter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $selectedMonth = $request->input('choose');
        $this->skipTime($selectedMonth);
        session(['selectedMonth' => $selectedMonth]);
        return redirect()->route('time.index');
    }

    /**
     * Führt die Logik für den Monatswechsel aus (z.B. neue Preise generieren).
     *
     * @param string $currentMonth Name des aktuellen Monats
     * @return void
     */
    public function skipTime($currentMonth)
    {
        $currentMonthIdx = $this->getMonthIndex($currentMonth);
        $stocks = Stock::all();

        //wichtig $stock->price()->get()->last()->month!!!
        foreach ($stocks as $stock) {
            
            $lastDate = $stock->price()->get()->last()->date;
            $lastDate = date("Y-m-d", strtotime($lastDate));
            dd($lastDate);
            
        }


        //dd(['CurrentMonth' => ['idx' => $currentMonthIdx, 'name' => $currentMonth], 'lastMonth' => ['idx' => $lastMonthIdx, 'name' => $lastMonth->month, 'id' => $lastMonth->id]]);
    }

    /**
     * Gibt den Index eines Monatsnamens im monthArray zurück.
     *
     * @param string $month Monatsname
     * @return int|null Index des Monats oder null, falls nicht gefunden
     */
    public function getMonthIndex($month)
    {
        $formattedMonth = ucfirst(strtolower($month));
        return array_search($formattedMonth, $this->monthArray);
    }

    /**
     * Berechnet das Datum des nächsten Monats.
     *
     * @param string $monthName Monatsname
     * @return string Datum des nächsten Monats (YYYY-MM-DD)
     */
    function getNextMonthDate($monthName) {
        $month = date("n", strtotime($monthName)); // Konvertiere Monatsname in Zahl
        $currentYear = date("Y");
        $currentMonth = date("n");

        // Falls der Monat schon vorbei ist, nehme das nächste Jahr
        $targetYear = ($currentMonth >= $month) ? $currentYear + 1 : $currentYear;

        return date("Y-m-d", strtotime("first day of $monthName $targetYear"));
    }
}
