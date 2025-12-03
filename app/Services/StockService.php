<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Events\TimeskipCompleted;

use Illuminate\Support\Facades\Log;

use App\Services\DividendeService;

use App\Models\Stock\Stock;
use App\Models\Stock\Price;
use App\Models\Config;
use App\Models\Dividend;
use App\Models\Stock\Transaction;
use App\Models\BuyTransaction;

class StockService
{
    /**
     * Gesamtwert des Portfolios inkl. Bankguthaben - Optimierte Version
     */
    public function getTotalPortfolioValue(): float
    {
        return $this->getTotalStockValue() + (Auth::user()->bank?->balance ?? 0);
    }

    /**
     * Gesamtwert der Aktien im Depot (ohne Bankguthaben)
     */
    public function getTotalStockValue(): float
    {
        $user = Auth::user();

        // Use the same statistic objects as the depot overview so profit/loss is
        // calculated consistently across views. This ensures the stock detail
        // page shows the same G/V as the depot listing.
        $stats = $this->getUserStocksWithStatistiks($user);

        return (float) $stats->sum(function ($stat) {
            // each $stat is an object created by getStockStatistiks
            return ($stat->quantity ?? 0) * ($stat->current_price ?? 0);
        });
    }

    /**
     * Depotwert basierend auf gespeicherten Kaufpreisen (nur aktuelle Holdings)
     */
    public function getTotalDepotValue(): float
    {
        $user = Auth::user();

        return $this->getUserStocksWithStatistiks($user)->sum(fn($stat) => $stat->avg_buy_price * $stat->quantity);
    }

    /**
     * Berechnet den Preis einer Transaktion
     */
    public function getPriceAtBuyForTransaction($transaction): float
    {
        // Use resolvedPriceAtBuy which handles stored value and fallbacks
        return (float) ($transaction->computeResolvedPriceAtBuy() ?? $transaction->stock?->getCurrentPrice() ?? 0);
    }

    /**
     * Durchschnittlicher Kaufpreis
     */
    public function calculateAverageBuyPrice($transactions, $beforeDate = null): float
    {
        if (empty($transactions)) {
            return 0;
        }

        $filtered = collect($transactions);

        if ($beforeDate) {
            // Datum auf Vormonat setzen
            $beforeDate = Carbon::parse($beforeDate)->startOfMonth()->subDay(); // letztes Datum des Vormonats
            $filtered = $filtered->filter(function ($t) use ($beforeDate) {
                $transactionDate = $t->gameTime?->name;
                return $transactionDate && Carbon::parse($transactionDate)->lte($beforeDate);
            });
        }

        $totalQuantity = $filtered->sum('quantity');
        $totalCost = $filtered->sum(function ($t) {
            return $t->quantity * ($t->price_at_buy ?? 0);
        });

        if ($totalQuantity <= 0) {
            return 0;
        }

        return $totalCost / $totalQuantity;
    }

    /**
     * Aggregierte Kennzahlen pro Aktie
     */
    public function getStockStatistiks($transactions, $user, int $currentMonth = null, $gameTime = null)
    {
        if ($transactions instanceof Collection && $transactions->first() instanceof Stock) {
            $transactions = $transactions->all();
        } elseif ($transactions instanceof Stock) {
            $stock = $transactions;
            $transactions = $this->getUserBuyTransactionsForStock($user, $stock->id)->all();
        } else {
            if (empty($transactions) || !isset($transactions[0])) {
                throw new \InvalidArgumentException('No transactions provided for stock statistics calculation');
            }
            $stock = $transactions[0]->stock;
            $transactions = $transactions->all();
        }

        $totalQuantity = $stock->getCurrentQuantity($user); // Use consistent quantity calculation
        $buyPrice = $this->calculateAverageBuyPrice($transactions);
        $firstBuyDate = $stock->getFirstBuyTransactionDateForStock();
        $lastBuyDate = $stock->getLastBuyTransactionDateForStock();
        $currentPrice = $stock->getCurrentPrice();
        $profitLoss = ($currentPrice - $buyPrice) * $totalQuantity; // Fix profit calculation
        $profitLossPercent = $buyPrice > 0 ? ($currentPrice - $buyPrice) / $buyPrice * 100 : 0;
        $depositShareInPercent = $this->getDepositShareInPercent($user, $stock);

        $dividendService = new DividendeService();
        $dividends = $dividendService->getDividendStatisticsForStock($stock, $user);

        \Log::debug('StockService@getStockStatistiks - dividend data for stock', [
            'stock_id' => $stock->id,
            'dividende' => $dividends,
        ]);

        return (object) [
            'stock' => $stock,
            'current_price' => $currentPrice,
            'avg_buy_price' => $buyPrice,
            'quantity' => $totalQuantity,
            'last_buy' => Carbon::parse($lastBuyDate)->format('d.m.Y'),
            'first_buy' => Carbon::parse($firstBuyDate)->format('d.m.Y'),
            'profit_loss' => $profitLoss,
            'profit_loss_percent' => $profitLossPercent,
            'deposit_share_in_percent' => $depositShareInPercent,
            'dividende' => $dividends,
        ];
    }

    /**
     * Alle Aktien eines Users - Optimierte Version mit Eager Loading
     */
    public function getUserStocks($user)
    {
        // Eager Loading der Stock-Daten mit nur benötigten Feldern
        $stockIds = Transaction::where('user_id', $user->id)
            ->where('type', 'buy')
            ->pluck('stock_id')
            ->unique();

        return Stock::whereIn('id', $stockIds)
            #->select('id', 'name', 'firma', 'sektor', 'land') // Nur benötigte Felder
            ->with(['prices' => function ($query) {
                $query->select('id', 'stock_id', 'name', 'game_time_id')
                    ->orderBy('game_time_id', 'desc')
                    ->take(1); // Nur der neueste Preis
            }])
            ->get();
    }

    /**
     * Alle Aktien mit Statistiken
     */
    public function getUserStocksWithStatistiks($user = null, int $currentMonth = null)
    {
        $user = $user ?? Auth::user();

        return $this->getUserStocks($user)
            ->filter(fn($stock) => $stock->getCurrentQuantity($user) > 0) // Nur Aktien mit positiver Menge einbeziehen
            ->map(fn($stock) => $this->getStockStatistiks($stock, $user, $currentMonth))
            ->values();
    }

    /**
     * Buy-Transaktionen für eine Aktie
     */
    public function getUserBuyTransactionsForStock($user, $stockID)
    {
        // Only consider buy transactions since the last time the user's holding dropped to zero
        $relevant = $this->getTransactionsSinceLastZero($user, $stockID);

        $filtered = $relevant
            ->where('type', 'buy')
            ->where('quantity', '>', 0);

        // Sort by GameTime when available, otherwise by created_at.
        $sorted = $filtered->sort(function ($a, $b) {
            $aKey = $a->game_time_id ?? 0;
            $bKey = $b->game_time_id ?? 0;
            if ($aKey === $bKey) {
                return strtotime($a->created_at) <=> strtotime($b->created_at);
            }
            return $aKey <=> $bKey;
        });

        return $sorted->values();
    }

    /**
     * Liefert alle Transaktionen für eine Aktie des Users, aber nur die
     * Transaktionen, die nach dem letzten Zeitpunkt kommen, an dem die
     * Holding für diese Aktie null war. So werden alte Käufe/Verkäufe
     * ignoriert, wenn die Aktie vollständig verkauft und später neu gekauft wurde.
     *
     * @param $user
     * @param int $stockID
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionsSinceLastZero($user, int $stockID)
    {
        // Hol alle Transaktionen (buy und sell) sortiert primär nach GameTime, dann nach created_at
        // Ignoriere andere Transaktionstypen wie Dividenden für die Zero-Berechnung
        $txns = Transaction::where('user_id', $user->id)
            ->where('stock_id', $stockID)
            ->whereIn('type', ['buy', 'sell'])
            ->orderBy('game_time_id')
            ->orderBy('created_at')
            ->get();

        if ($txns->isEmpty()) {
            return $txns;
        }

        $cumulative = 0;
        $lastZeroIndex = null;

        foreach ($txns->values() as $i => $t) {
            // Normalisierung: buys add, sells subtract
            if (($t->type ?? '') === 'buy') {
                $cumulative += ($t->quantity ?? 0);
            } elseif (($t->type ?? '') === 'sell') {
                $cumulative -= ($t->quantity ?? 0);
            }

            // When cumulative is exactly zero, mark this index as reset point
            if ($cumulative === 0) {
                $lastZeroIndex = $i;
            }
        }

        if (is_null($lastZeroIndex)) {
            return $txns;
        }

        // Return all transactions after the last index where cumulative was zero
        return $txns->slice($lastZeroIndex + 1)->values();
    }

    /**
     * Anteil der Aktie am Depot basierend auf aktuellem Wert (nicht investiertem Kapital)
     */
    public function getDepositShareInPercent($user, $stock, $gameTime = null)
    {
        // Aktueller Wert dieser Aktie im Depot
        $currentPrice = $gameTime ? $stock->getPriceAtGameTime($gameTime) : $stock->getCurrentPrice();
        $currentStockValue = $stock->getCurrentQuantity($user) * $currentPrice;

        // Gesamtwert aller Aktien im Depot (nur Aktien mit positiver Menge)
        $totalPortfolioValue = $this->getUserStocks($user)
            ->filter(fn($s) => $s->getCurrentQuantity($user) > 0)
            ->sum(function ($s) use ($user, $gameTime) {
                $price = $gameTime ? $s->getPriceAtGameTime($gameTime) : $s->getCurrentPrice();
                return $s->getCurrentQuantity($user) * $price;
            });

        if ($totalPortfolioValue == 0) {
            return 0; // Vermeidung von Division durch Null
        }

        return number_format((float) ($currentStockValue / $totalPortfolioValue) * 100, 2, '.', '');
    }

    /**
     * Alle Aktien eines Users mit aktuellen Holdings
     */
    public static function getUserStocksWithHoldings($user)
    {
        return $user->transactions
            ->where('type', 'buy')
            ->groupBy('stock_id')
            ->map(function ($group) {
                $stock = $group[0]->stock;
                $quantity = $stock->getCurrentQuantity();
                return [
                    'stock' => $stock,
                    'quantity' => $quantity
                ];
            })
            ->filter(fn($item) => $item['quantity'] > 0)
            ->values()
            ->toArray();
    }

    public static function processNewTimeSteps($newGameTimes, $stocks)
    {
        $priceService = new PriceService();
        $gtService = new GameTimeService();

        // Für jede neue GameTime, für jede Stock Preise und Dividenden berechnen
        foreach ($newGameTimes as $newGameTime) {
            $monthIndex = (int) date('m', strtotime($newGameTime->name)) - 1; // 0-based für generatePrice

            foreach ($stocks as $stock) {
                // Letzten Preis holen
                $lastPrice = $stock->getLatestPrice();
                if (!$lastPrice) {
                    $lastPrice = 100.0; // Fallback, wenn kein Preis vorhanden
                }
                $lastDividend = $stock->getLatestDividend();
                if (is_null($lastDividend)) {
                    $lastDividendDate = $stock->calculateNextDividendDate($newGameTime->name);
                    $lastDividendGT = Dividend::factory()->create([
                        'stock_id' => $stock->id,
                        'game_time_id' => $gtService->getOrCreate($lastDividendDate)->id,
                        'amount_per_share' => fake()->randomFloat(2, 0.1, 5.0),
                    ]);
                    \Log::info("Created initial dividend for stock {$stock->id} at game time {$lastDividendGT->game_time_id}");
                } else {
                    $lastDividendGT = $lastDividend->gameTime;
                }

                // Stock-spezifische Config laden (oder Default verwenden)
                $stockConfig = $stock->getCurrentConfig();

                // Neuen Preis berechnen mit generatePrice und der Stock-Config
                $newPriceValue = $priceService->generatePrice($lastPrice, $monthIndex, $stockConfig);

                // Preis-Eintrag für diesen Monat erzeugen (manuell)
                Price::create([
                    'stock_id' => $stock->id,
                    'game_time_id' => $newGameTime->id,
                    'name' => $newPriceValue,
                ]);

                // Prüfen, ob Dividende fällig ist (Datum <= aktuelle GameTime)
                if (Carbon::parse($lastDividendGT->name)->lte($newGameTime->name)) {
                    
                    // Verhindere doppelte Dividenden im selben Monat
                    $exists = $stock->dividends()
                        ->where('game_time_id', $newGameTime->id)
                        ->exists();

                    if (!$exists) {

                        // Job dispatchen für asynchrone Ausführung
                        \App\Jobs\ProcessDividendPayout::dispatch($stock->id);

                        \Log::info("Dividend payout job dispatched for stock {$stock->id}");

                        // Nächste Dividenden-Spielzeit für das Datum holen
                        $nextDividendDate = $stock->calculateNextDividendDate($newGameTime->name);
                        $nextDividendGT = $gtService->getOrCreate($nextDividendDate);

                        // Neue Dividende erzeugen
                        Dividend::create([
                            'stock_id' => $stock->id,
                            'game_time_id' => $nextDividendGT->id,
                            'amount_per_share' => fake()->randomFloat(2, 0.1, 5.0),
                        ]);

                        \Log::info("Dividend due for stock {$stock->id} at game time {$newGameTime->name}");
                    } else {
                        \Log::debug("Dividend already exists for stock {$stock->id} at game time {$newGameTime->name}, skipping");
                    }
                }
            }
        }
    }
}
