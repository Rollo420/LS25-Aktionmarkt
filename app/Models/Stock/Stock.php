<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Stock\Price;
use App\Models\Stock\Transaction;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = ['name',];

    public function price()
    {
        return $this->hasMany(Price::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
