<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Account\Account;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'created_at', 'updated_at', 'stock_id', 'quantity', 'account_id', 'status'];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
