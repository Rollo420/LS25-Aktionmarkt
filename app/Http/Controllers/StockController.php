<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock\Stock;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index()
    {
        $stockWithPrice= [];
        $stocks = Stock::all();
        
        foreach ($stocks as $stock)
        {              
            array_push($stockWithPrice, [$stock->id ,$stock->name ,$stock->price->last()->name]);            
        }
        // dd($stockWithPrice);
        return view('Stock.index', ['stocks' => $stockWithPrice]);
    }

    public function store($id)
    {
        $stock = Stock::findOrFail($id);
        return view('Stock.show', ['stock' => $stock]);
    }
}
