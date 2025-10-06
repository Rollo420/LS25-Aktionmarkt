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
    protected $fillable = ['id', 'created_at', 'updated_at', 'stock_id', 'quantity', 'user_id', 'status', 'price', 'type'];

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
}
