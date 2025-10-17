<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Stock\Stock;
use App\Models\BuyTransaction;
use App\Models\SellTransaction;
use App\Services\StockService;

class OrderController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request, $stockID = null)
    {
        $user = $request->user();
        $stock = Stock::findOrFail($stockID);

        DB::beginTransaction();

        try {
            $currentMonth = $request->input('current_month', 1); // aktueller Ingame-Monat

            if ($request->has('buy')) {
                $quantityToBuy = $request->input('quantity');
                $currentPrice = $stock->getCurrentPrice();

                // Letzte offene Käufe
                $previousBuys = BuyTransaction::where('user_id', $user->id)
                    ->where('stock_id', $stock->id)
                    ->where('quantity', '>', 0)
                    ->get();

                // Gewichteter Durchschnittspreis
                $totalQuantity = $previousBuys->sum('quantity') + $quantityToBuy;
                $totalCost = $previousBuys->reduce(function ($carry, $t) {
                    return $carry + ($t->quantity * $t->price_at_buy);
                }, 0) + ($quantityToBuy * $currentPrice);

                $averagePrice = $totalQuantity > 0 ? $totalCost / $totalQuantity : $currentPrice;

                // Neue BuyTransaction erstellen
                $buyTransaction = new BuyTransaction();
                $buyTransaction->user_id = $user->id;
                $buyTransaction->stock_id = $stock->id;
                $buyTransaction->quantity = $quantityToBuy;
                $buyTransaction->price_at_buy = $averagePrice;
                $buyTransaction->type = 'buy';
                $buyTransaction->status = 'close';
                $buyTransaction->ingame_month = $currentMonth; // Ingame-Monat speichern
                $buyTransaction->save();

                // Bank prüfen und abbuchen
                $bank = $user->bank()->first();
                $totalCostForThisBuy = $quantityToBuy * $currentPrice;

                if ($bank->balance < $totalCostForThisBuy) {
                    throw new \Exception('Nicht genügend Guthaben für diesen Kauf.');
                }

                $bank->balance -= $totalCostForThisBuy;
                $bank->save();

                DB::commit();
                return redirect()->back()->with('success', 'Kauf erfolgreich! Neuer Kontostand: ' . $bank->balance);
            }

            if ($request->has('sell')) {
                $sellQuantity = $request->input('quantity');

                // SellTransaction erstellen
                $sellTransaction = new SellTransaction();
                $sellTransaction->user_id = $user->id;
                $sellTransaction->stock_id = $stock->id;
                $sellTransaction->quantity = $sellQuantity;
                $sellTransaction->status = 'close';
                $sellTransaction->type = 'sell';
                $sellTransaction->ingame_month = $currentMonth; // Ingame-Monat speichern

                // Alte Käufe abrufen (FIFO)
                $buyTransactions = BuyTransaction::where('user_id', $user->id)
                    ->where('stock_id', $stock->id)
                    ->where('quantity', '>', 0)
                    ->where('type', 'buy')
                    ->orderBy('ingame_month', 'asc')
                    ->get();

                $totalAvailable = $buyTransactions->sum('quantity');
                if ($totalAvailable < $sellQuantity) {
                    throw new \Exception('Nicht genügend offene Kaufpositionen. Du besitzt nur: ' . $totalAvailable);
                }

                // FIFO Verkauf
                foreach ($buyTransactions as $buy) {
                    if ($sellQuantity <= 0)
                        break;
                    $toSell = min($buy->quantity, $sellQuantity);
                    $buy->quantity -= $toSell;

                    if ($buy->quantity == 0) {
                        $buy->status = 'closed';
                    }
                    $buy->save();
                    $sellQuantity -= $toSell;
                }

                // Bank gutschreiben
                $bank = $user->bank()->first();
                $bank->balance += $sellTransaction->quantity * $stock->getCurrentPrice();
                $bank->save();

                $sellTransaction->save();

                DB::commit();
                return redirect()->back()->with('success', 'Verkauf erfolgreich! Neuer Kontostand: ' . $bank->balance);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
