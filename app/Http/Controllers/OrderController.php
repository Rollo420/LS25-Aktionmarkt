<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;

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
            $currentMonth = (int) $request->input('current_month', 1); // aktueller Ingame-Monat (1-12)
            // find or fallback GameTime for the selected month
            // Prefer an existing GameTime for the requested month, otherwise create via GameTimeService
            $gtService = new \App\Services\GameTimeService();
            $candidate = \App\Models\GameTime::where('name', date('Y-m-d', strtotime(date('Y') . '-' . $currentMonth . '-01')))->latest()->first()
                ?? \App\Models\GameTime::latest()->first();
            if ($candidate) {
                $gameTime = $candidate;
            } else {
                $gameTime = $gtService->getOrCreate(Carbon::create(date('Y'), $currentMonth, 1));
            }
            $bank = $user->bank()->first();

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
                    $price = $t->resolvedPriceAtBuy() ?? $t->stock?->getCurrentPrice() ?? 0;
                    return $carry + ($t->quantity * $price);
                }, 0) + ($quantityToBuy * $currentPrice);

                $averagePrice = $totalQuantity > 0 ? $totalCost / $totalQuantity : $currentPrice;

                // Neue BuyTransaction erstellen
                $buyTransaction = new BuyTransaction();
                $buyTransaction->user_id = $user->id;
                $buyTransaction->stock_id = $stock->id;
                $buyTransaction->quantity = $quantityToBuy;
                $buyTransaction->price_at_buy = $averagePrice;
                $buyTransaction->type = 'buy';
                $buyTransaction->status = false; // closed
                $buyTransaction->game_time_id = $gameTime->id; // link to game_time

                $totalCostForThisBuy = $quantityToBuy * $currentPrice;

                if ($bank->balance < $totalCostForThisBuy) {
                    throw new \Exception('Nicht genügend Guthaben für diesen Kauf.');
                }

                $bank->balance -= $totalCostForThisBuy;
                $bank->save();

                $buyTransaction->save();

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
                $sellTransaction->price_at_buy = $stock->getCurrentPrice();
                $sellTransaction->status = false; // closed
                $sellTransaction->type = 'sell';
                $sellTransaction->game_time_id = $gameTime->id; // link to game_time

                // Alte Käufe abrufen (FIFO)
                $buyTransactions = BuyTransaction::where('user_id', $user->id)
                    ->where('stock_id', $stock->id)
                    ->where('quantity', '>', 0)
                    ->where('type', 'buy')
                    ->orderBy('game_time_id', 'asc')
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
                        $buy->status = false; // closed
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
