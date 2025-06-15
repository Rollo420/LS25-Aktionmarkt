<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Parental\HasParent;

use DepositTransaction;
use BuyTransaction;
use WithdrawTransaction;
use SellTransaction;

use App\Models\User;

class Transaction extends Model
{
    use HasFactory;
    /**
     * Die Attribute, die massenweise zuweisbar sind.
     *
     * @var array
     */
    protected $fillable = ['id', 'created_at', 'updated_at', 'stock_id', 'quantity', 'user_id', 'status'];

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
}
