<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeController extends Controller
{
    public $monthArray =  ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    public function index()
    {
        $selectedMonth = session('selectedMonth', 'None');
        return view('time.index', ['monthArray' => $this->monthArray, 'selectedMonth' => $selectedMonth]);
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
        //dd($currentMonth);
    }

    public function getMonthIndex($month)
    {
        $formattedMonth = ucfirst(strtolower($month));
        return array_search($formattedMonth, $this->monthArray);
    }

}
