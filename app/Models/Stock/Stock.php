<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Stock\Price;
use App\Models\Stock\Product_type;
use App\Models\Stock\Transaction;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'price_id', 'product_types_id'];

    public function price()
    {
        return $this->belongsTo(Price::class, 'price_id');
    }

    public function productType()
    {
        return $this->belongsTo(Product_type::class, 'product_type_id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
