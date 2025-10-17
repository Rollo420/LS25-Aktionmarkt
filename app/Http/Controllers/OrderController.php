<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock\Stock;
use App\Models\BuyTransaction;
use App\Models\SellTransaction;
use App\Models\Stock\Transaction;
use Log;

class OrderController extends Controller
{
    public function index(Request $request, $stockID = null)
    {
        $user = $request->user();
        $stock = Stock::findOrFail($stockID);

        DB::beginTransaction();
        if ($request->has('buy')) 
        {
            try {
                $buyTransaction = new BuyTransaction;
                $buyTransaction->user_id = $user->id;
                $buyTransaction->stock_id = $stock->id;
                $buyTransaction->quantity = $request->input('quantity'); // Stückzahl aus Formular
                $buyTransaction->status = 'close'; // Anfangsstatus
                $buyTransaction->type = 'buy'; // Transaktionstyp
                $buyTransaction->price_at_buy = $stock->getCurrentPrice();
                
                $user->refresh(); // Aktualisiert die Benutzerdaten
                
                $bank = $user->bank()->first(); // Hole die Bank-Instanz eindeutig
                $latestPrice = $stock->prices->last()->name; // Hole letzten Preis wie im StockController
                
                $bank->refresh();
                $newBalance = ($latestPrice * $buyTransaction->quantity);

                if ($bank->balance < 0) {
                    throw new \Exception('Nicht genügend Guthaben für diesen Kauf.');
                }
                else if ($bank->balance < $newBalance)
                {
                    throw new \Exception('Nicht genügend Guthaben für diesen Kauf.');
                }

                $bank->balance -= $newBalance;

                $buyTransaction->save();
                $bank->save();
                
                DB::commit();
                return redirect()->back()->with('success', 'Kauf erfolgreich! Neuer Kontostand: ' . $bank->balance);
            }
            catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Kauf fehlgeschlagen: ' . $e->getMessage());
            }
        }
        else if($request->has('sell')) 
        {
            try {
                $sellTransaction = new SellTransaction;
                $sellTransaction->user_id = $user->id;
                $sellTransaction->stock_id = $stock->id;
                $sellTransaction->quantity = $request->input('quantity');
                $sellTransaction->status = 'close';
                $sellTransaction->type = 'sell';

                $user->refresh();
                $bank = $user->bank()->first();
                $latestPrice = $stock->prices->last()->price;

                $sellQuantity = $sellTransaction->quantity;
                $buyTransactions = BuyTransaction::where('user_id', $user->id)
                    ->where('stock_id', $stock->id)
                    ->where('quantity', '>', 0)
                    ->where('type', 'buy')
                    ->orderBy('created_at', 'asc')
                    ->get();

                $totalAvailable = $buyTransactions->sum('quantity');
                if ($buyTransactions->isEmpty() || $totalAvailable < $sellQuantity) {
                    throw new \Exception('Nicht genügend offene Kaufpositionen für diesen Verkauf. Du besitzt insgesamt: ' . $totalAvailable . ' Stück.');
                }

                foreach ($buyTransactions as $buy) {
                    if ($sellQuantity <= 0) break;
                    $available = $buy->quantity;
                    $toSell = min($available, $sellQuantity);
                    $buy->quantity = ($buy->quantity - $toSell);
                    if ($buy->quantity == 0) {
                        $buy->status = 'closed';
                        $buy->type = 'sell';
                    }
                    $buy->save();
                    $sellQuantity -= $toSell;
                }

                $bank->refresh();
                $bank->balance += ($latestPrice * $sellTransaction->quantity);

                $sellTransaction->save();
                $bank->save();

                DB::commit();
                return redirect()->back()->with('success', 'Verkauf erfolgreich! Neuer Kontostand: ' . $bank->balance);
            }
            catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Verkauf fehlgeschlagen: ' . $e->getMessage());
            }
        }

        //dd(BuyTransaction::all());

    }
}
