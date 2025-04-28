<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
