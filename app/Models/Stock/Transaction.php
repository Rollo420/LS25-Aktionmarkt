<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Collection;
use Parental\HasChildren;

use App\Models\DepositTransaction;
use App\Models\BuyTransaction;
use App\Models\WithdrawTransaction;
use App\Models\SellTransaction;

use App\Models\User;

class Transaction extends Model
{
    use HasFactory;
    /**
     * Die Attribute, die massenweise zuweisbar sind.
     *
     * @var array
     */
    protected $fillable = ['id', 'created_at', 'updated_at', 'stock_id', 'quantity', 'user_id', 'status', 'price_at_buy', 'type', 'game_time_id'];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Beziehung: Eine Transaktion gehört zu einer Aktie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    /**
     * Beziehung: Eine Transaktion gehört zu einem User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Beziehung: Transaktion gehört zu einem GameTime Eintrag
     */
    public function gameTime()
    {
        return $this->belongsTo(\App\Models\GameTime::class, 'game_time_id');
    }

    protected $childTypes = [
        'deposit' => DepositTransaction::class,
        'buy' => BuyTransaction::class,
        'withdraw' => WithdrawTransaction::class,
        'sell' => SellTransaction::class,
    ];

    public function getStockPrices()
    {
        return $this->hasManyThrough(
            Price::class, // Zielmodell
            Stock::class, // Zwischentabelle
            'id',        // Foreign Key in Stock (Stock::id)
            'stock_id',  // Foreign Key in Price (Price::stock_id)
            'stock_id',  // Lokaler Key in Transaction (Transaction::stock_id)
            'id'         // Lokaler Key in Stock (Stock::id)
        );
    }

    /**
     * Resolve the effective price for this transaction's buy price.
     * Returns stored price_at_buy if present and > 0, otherwise
     * attempts to find a Price by game_time_id for the stock, then
     * nearest earlier Price, then falls back to stock current price.
     * Returns null if no price can be determined.
     *
     * @return float|null
     */
    public function resolvedPriceAtBuy(): ?float
    {
        // Use persisted value if available
        if (isset($this->price_at_buy) && $this->price_at_buy !== null && $this->price_at_buy > 0) {
            return (float) $this->price_at_buy;
        }

        // Need a stock to lookup prices
        if (!isset($this->stock_id) || !$this->stock_id) {
            return null;
        }

        try {
            $stock = $this->stock ?? \App\Models\Stock\Stock::find($this->stock_id);
            if ($stock) {
                // If we have a game_time_id, try exact match first
                if (isset($this->game_time_id) && $this->game_time_id) {
                    $priceObj = $stock->prices()->where('game_time_id', $this->game_time_id)->latest('id')->first();
                    if ($priceObj) {
                        return (float) ($priceObj->name ?? $priceObj->price ?? 0);
                    }
                    // nearest earlier price by game_time_id
                    $priceObj = $stock->prices()->where('game_time_id', '<', $this->game_time_id)->orderByDesc('game_time_id')->first();
                    if ($priceObj) {
                        return (float) ($priceObj->name ?? $priceObj->price ?? 0);
                    }
                }

                // fallback: latest price by created_at
                $priceObj = $stock->prices()->latest('created_at')->first();
                if ($priceObj) {
                    return (float) ($priceObj->name ?? $priceObj->price ?? 0);
                }

                // last resort: stock current price
                return (float) $stock->getCurrentPrice();
            }
        } catch (\Throwable $e) {
            // swallow lookup errors and return null to let callers decide
            return null;
        }

        return null;
    }
}
