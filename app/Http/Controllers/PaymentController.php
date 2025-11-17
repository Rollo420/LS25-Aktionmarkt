<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentService;
use App\Services\GameTimeService;

use App\Models\Stock\Transaction;
use App\Models\User;
use App\Models\Bank;

use App\Http\Requests\PayinRequest;
use App\Http\Requests\PayoutRequest;
use App\Http\Requests\TransferRequest;

class PaymentController extends Controller
{
    public function index()
    {
        $transaktionen = Transaction::where('user_id', auth()->id())
            ->with(['stock', 'gameTime']) // Eager load relations
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return $transaction;
            });
        $orders = Transaction::where('user_id', auth()->id())
            ->whereIn('type', ['buy', 'sell', 'deposit', 'withdraw', 'transfer'])
            ->where('status', true)
            ->with(['stock', 'gameTime']) // Eager load relations
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
    public function payin(PayinRequest $request)
    {
        try {

            $payin = new Transaction();

            $payin->type = 'deposit';
            $payin->status = true; // pending approval
            $payin->quantity = $request->input('payin');
            $payin->user_id = auth()->id();

            // Attach current game_time_id so DB-V2 semantics are respected
            $gts = new GameTimeService();
            $gt = $gts->getOrCreateByYearMonth((int) date('Y'), (int) date('m'));
            $payin->game_time_id = $gt->id;
            // deposit has no price at buy
            $payin->price_at_buy = null;

            $payin->save();
        } catch (\Exception $e) {
            return redirect()->route('payment.index')->with('error', 'Error processing pay-in: ' . $e->getMessage());
        }

    return redirect()->route('payment.index')->with('success', 'Pay-in processed successfully!');
    }

    public function payout(PayoutRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            $payout = new Transaction();

            $payout->type = 'withdraw';
            $payout->status = true; // open
            $payout->quantity = $request->input('payout');
            $payout->user_id = $user->id;

            // Attach current game_time
            $gts = new GameTimeService();
            $gt = $gts->getOrCreateByYearMonth((int) date('Y'), (int) date('m'));
            $payout->game_time_id = $gt->id;
            // withdraw has no price at buy
            $payout->price_at_buy = null;

            if (PaymentService::checkUserBalance($payout->quantity)) {
                $payout->save();
            } else {
                throw new \Exception('Nicht genug Guthaben für die Auszahlung!');
            }


        } catch (\Exception $e) {
            return redirect()->route('payment.index')->with('error', 'Error processing pay-out: ' . $e->getMessage());
        }

        return redirect()->route('payment.index')->with('success', 'Pay-out processed successfully!');
    }


    public function transfer(TransferRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $from = User::findOrFail(auth()->id());

                try {
                    $toBank = Bank::where('iban', $request->input('to_account'))->lockForUpdate()->firstOrFail();
                } catch (\Exception $e) {
                    throw new \Exception('Die angegebende IBAN ist nicht gültig!');
                }

                $amount = $request->input('amount');

                // Hole die Bank-Relationen über das User-Model
                $fromBank = $from->bank()->lockForUpdate()->first();

                // dd($toBank);

                if (!$fromBank || !$toBank) {
                    throw new \Exception('Bankkonto nicht gefunden');
                }

                if (PaymentService::checkUserBalance($amount)) {
                    // Update balances
                    $fromBank->balance -= $amount;
                    $fromBank->save();

                    $toBank->balance += $amount;
                    $toBank->save();

                    $transfer = new Transaction();
                    $transfer->type = 'transfer';
                    $transfer->status = true; // confirmed -> final
                    $transfer->quantity = $amount;
                    $transfer->user_id = auth()->id();

                    // Attach current game_time
                    $gts = new GameTimeService();
                    $gt = $gts->getOrCreateByYearMonth((int) date('Y'), (int) date('m'));
                    $transfer->game_time_id = $gt->id;
                    // transfer has no price at buy
                    $transfer->price_at_buy = null;

                    $transfer->save();
                }

            });
        } catch (\Exception $e) {
            return redirect()->route('payment.index')->with('error', 'Error processing transfer: ' . $e->getMessage());
        }
        return redirect()->route('payment.index')->with('success', 'Transfer processed successfully!');
    }

    public function transaction()
    {
        $transaktionen = Transaction::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return $transaction;
            });
        return view('payment.index', [
            'transaktionens' => $transaktionen,
        ]);
    }

   
}
