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
    protected $stockService;

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
            if ($request->has('buy')) {
                $quantityToBuy = (int) $request->input('quantity');
                $currentPrice = $stock->getCurrentPrice();

                $previousBuys = $this->stockService->getUserBuyTransactionsForStock($user, $stock->id);

                $totalQuantity = $previousBuys->sum('quantity') + $quantityToBuy;
                $totalCost = $previousBuys->reduce(fn($carry, $t) => $carry + ($t->quantity * $t->price_at_buy), 0)
                    + ($quantityToBuy * $currentPrice);

                $averagePrice = $totalQuantity > 0 ? $totalCost / $totalQuantity : $currentPrice;

                $bank = $user->bank()->first();
                $totalCostForThisBuy = $quantityToBuy * $currentPrice;
                if ($bank->balance < $totalCostForThisBuy) {
                    throw new \Exception('Nicht genügend Guthaben.');
                }

                $bank->balance -= $totalCostForThisBuy;

                $buyTransaction = new BuyTransaction();
                $buyTransaction->user_id = $user->id;
                $buyTransaction->stock_id = $stock->id;
                $buyTransaction->quantity = $quantityToBuy;
                $buyTransaction->status = 'close';
                $buyTransaction->type = 'buy';
                $buyTransaction->price_at_buy = $averagePrice;
                $buyTransaction->save();

                $bank->save();

                DB::commit();
                return redirect()->back()->with('success', 'Kauf erfolgreich! Neuer Kontostand: ' . $bank->balance);
            }

            if ($request->has('sell')) {
                $sellTransaction = new SellTransaction();
                $sellTransaction->user_id = $user->id;
                $sellTransaction->stock_id = $stock->id;
                $sellTransaction->quantity = (int) $request->input('quantity');
                $sellTransaction->status = 'close';
                $sellTransaction->type = 'sell';

                $sellQuantity = $sellTransaction->quantity;
                $buyTransactions = $this->stockService->getUserBuyTransactionsForStock($user, $stock->id);
                $totalAvailable = $buyTransactions->sum('quantity');

                if ($totalAvailable < $sellQuantity) {
                    throw new \Exception("Nicht genügend offene Kaufpositionen. Du hast nur {$totalAvailable} Stück.");
                }

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

                $bank = $user->bank()->first();
                $bank->balance += ($stock->getCurrentPrice() * $sellTransaction->quantity);

                $sellTransaction->save();
                $bank->save();

                DB::commit();
                return redirect()->back()->with('success', 'Verkauf erfolgreich! Neuer Kontostand: ' . $bank->balance);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
