<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Stock\Transaction;

class PaymentController extends Controller
{
    public function index()
    {   
        return view('payment.index');
    }

    public function store(Request $request)
    {
        // Handle the payment logic here
        // For example, you can use a payment gateway API to process the payment

        // Redirect back to the payment page with a success message
        return redirect()->route('payment.index')->with('success', 'Payment processed successfully!');
    }
    // kaufen, verkaufen, einzahlen, abheben, überweisen
    // 'buy', 'sell', 'deposit', 'withdraw', 'transfer'
    public function payin(Request $request)
    {
        try
        {

            $payin = new Transaction();
    
            $payin->type = 'deposit';
            $payin->status = 'open';
            $payin->quantity = $request->input('payin'); 
            $payin->user_id = auth()->id();
            
            $payin->save();
        }
        catch (\Exception $e)
        {
            return redirect()->route('payment.index')->with('error', 'Error processing pay-in: ' . $e->getMessage());
        }

        return redirect()->route('payment.index')->with('success', 'Pay-in processed successfully!');
    }
}
