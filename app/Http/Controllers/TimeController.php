<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeController extends Controller
{
    public $monthArray =  ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    public function mainTime()
    {
        $this->SkipTime();

        return view('time.index', );
    }

    public function skipTime() 
    {
        $currentMonth = $this->getMonthIndex('february');
        //dd($currentMonth);
    }

    public function getMonthIndex($month)
    {
        $formattedMonth = ucfirst(strtolower($month));
        return array_search($formattedMonth, $this->monthArray);
    }

}
