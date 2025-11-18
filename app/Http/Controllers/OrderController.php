<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Stock\Stock;
use App\Models\BuyTransaction;
use App\Models\SellTransaction;
use \App\Models\GameTime;
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
            // Always use the latest GameTime for synchronization with Price
            $gameTime = GameTime::getCurrentGameTime();
            if (!$gameTime) {
                // If no GameTime exists, create one for current date
                $gtService = new \App\Services\GameTimeService();
                $gameTime = $gtService->getOrCreate(Carbon::now());
            }
            

            // Ensure a Price exists for this GameTime and Stock
            $existingPrice = \App\Models\Stock\Price::where('stock_id', $stock->id)
                ->where('game_time_id', $gameTime->id)
                ->first();
            if (!$existingPrice) {
                // Create a new Price with current price value
                $newPrice = new \App\Models\Stock\Price();
                $newPrice->stock_id = $stock->id;
                $newPrice->game_time_id = $gameTime->id;
                $newPrice->name = $stock->getCurrentPrice(); // Use current price as base
                $newPrice->save();
            }
            $bank = $user->bank()->first();
            $currentPrice = $stock->getCurrentPrice();  

            if ($request->has('buy')) {
                $quantityToBuy = $request->input('quantity');

                // Neue BuyTransaction erstellen
                $buyTransaction = new BuyTransaction();
                $buyTransaction->user_id = $user->id;
                $buyTransaction->stock_id = $stock->id;
                $buyTransaction->quantity = $quantityToBuy;
                $buyTransaction->price_at_buy = $currentPrice;
                $buyTransaction->type = 'buy';
                $buyTransaction->status = false; // closed - sofort ausgeführt
                $buyTransaction->game_time_id = $gameTime->id; // link to game_time

                $totalCostForThisBuy = $quantityToBuy * $currentPrice;

                if ($bank->balance < $totalCostForThisBuy) {
                    throw new \Exception('Nicht genügend Guthaben für diesen Kauf.');
                }

                $bank->balance -= $totalCostForThisBuy;
                $bank->save();

                $buyTransaction->save();

                DB::commit();
                return redirect()->back()->with('success', 'Kauf erfolgreich! Sie haben Anteile im Wert von ' . $totalCostForThisBuy . ' erworben.');
            }

            if ($request->has('sell')) {
                $sellQuantity = $request->input('quantity');
                $totalBuyQuantity = $stock->getCurrentQuantity($user);

                if($sellQuantity > $totalBuyQuantity) {
                    throw new \Exception('Sie können nicht mehr Aktien verkaufen, als Sie besitzen.');
                }
                
                // SellTransaction erstellen
                $sellTransaction = new SellTransaction();
                $sellTransaction->user_id = $user->id;
                $sellTransaction->stock_id = $stock->id;
                $sellTransaction->quantity = $sellQuantity;
                $sellTransaction->price_at_buy = $currentPrice;
                $sellTransaction->status = false; // closed - sofort ausgeführt
                $sellTransaction->type = 'sell';
                $sellTransaction->game_time_id = $gameTime->id; // link to game_time

                // Bank gutschreiben
                $bank = $user->bank()->first();
                $bank->balance += $sellTransaction->quantity * $currentPrice;
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
