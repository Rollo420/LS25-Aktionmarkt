<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Stock\Transaction;
use \App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentAuthorizationController extends Controller
{
    public function index()
    {
        $openTransactions = Transaction::where('status', 'open')
            ->whereIn('type', ['withdraw', 'deposit'])
            ->get();

        $allOpenTransactions = $openTransactions->all();

        //foreach($allOpenTransactions as $trans)
        //{
        //    dd($trans->id);
        //}

        return view('payment.payment-authorization' , ['payments' => $openTransactions->all()]);
    }

    public function handlePaymentApproval(Request $request)  {
        if ($request->has('authorize_id')) {
           
            try {
            
                DB::transaction(function () use ($request) {
                    $transaction = Transaction::findOrFail($request->input('authorize_id'));
                    $user = User::where('id', $transaction->user_id)->firstOrFail();
                    $bank = $user->bank()->lockForUpdate()->first();
                   
                   
                    DB::transaction(function () use ($bank, $transaction) {

                        if($transaction->type == 'withdraw') {
                            $bank->balance -= $transaction->quantity;                            
                        }
                        else
                        {
                            $bank->balance += $transaction->quantity;
                        }
                        
                        $transaction->status = 'confirmed';
            
                        $transaction->save();
                        $bank->save();
                        
                        
                    });
                });
            } catch (\Exception $e) {
                return redirect()->route('payment.auth')->with('error', 'Error processing confirmed: ' . $e->getMessage());
            }
            return redirect()->route('payment.auth')->with('success', 'Confirmed processed successfully!');

        }
        elseif ($request->has('decline_id')) 
        {
            try
            {
                $transaction = Transaction::findOrFail($request->input('decline_id'));
                $transaction->status = 'failed';
                $transaction->save();
            }
            catch (\Exception $e)
            {
                return redirect()->route('payment.auth')->with('error', 'Error processing decline: ' . $e->getMessage());
            }


            return redirect()->route('payment.auth')->with('success', 'Decline processed successfully!');

        }


        

    }

}

