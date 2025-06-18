<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Stock\Transaction;
use App\Models\User;
use App\Models\Bank;

class PaymentController extends Controller
{
    public function index()
    {   
        $transaktionen = Transaction::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        $orders = Transaction::where('user_id', auth()->id())
            ->whereIn('type', ['buy', 'sell', 'deposit', 'withdraw', 'transfer'])
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get();

        //dd($transaktionen, $orders);
        return view('payment.index', [
            'transaktionens' => $transaktionen,
            'orders' => $orders,
        ]);
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

    public function payout(Request $request)
    {
        try
        {

            $payout = new Transaction();

            $payout->type = 'withdraw';
            $payout->status = 'open';
            $payout->quantity = $request->input('payout');
            $payout->user_id = auth()->id();

            $payout->save();
        }
        catch (\Exception $e)
        {
            return redirect()->route('payment.index')->with('error', 'Error processing pay-out: ' . $e->getMessage());
        }

        return redirect()->route('payment.index')->with('success', 'Pay-out processed successfully!');
    }
   
   
    public function transfer(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $from = User::findOrFail(auth()->id());

                try
                {
                    $toBank = Bank::where('iban', $request->input('to_account'))->lockForUpdate()->firstOrFail();
                }
                catch (\Exception $e)
                {
                    throw new \Exception('Die angegebende IBAN ist nicht gültig!');
                }
                
                $amount = $request->input('amount');

                // Hole die Bank-Relationen über das User-Model
                $fromBank = $from->bank()->lockForUpdate()->first();

               // dd($toBank);

                if (!$fromBank || !$toBank) {
                    throw new \Exception('Bankkonto nicht gefunden');
                }

                if ($fromBank->balance < $amount) {
                    throw new \Exception('Nicht genug Guthaben');
                }


                // Update balances
                $fromBank->balance -= $amount;
                $fromBank->save();

                $toBank->balance += $amount;
                $toBank->save();

                $transfer = new Transaction();
                $transfer->type = 'transfer';
                $transfer->status = 'confirmed';
                $transfer->quantity = $amount;
                $transfer->user_id = auth()->id();
                $transfer->save();
            });
        } catch (\Exception $e) {
            return redirect()->route('payment.index')->with('error', 'Error processing transfer: ' . $e->getMessage());
        }
        return redirect()->route('payment.index')->with('success', 'Transfer processed successfully!');
    }

    public function transaktion()
    {
        $transaktionen = Transaction::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('payment.index', [
            'transaktionens' => $transaktionen,
        ]);
    }

}
