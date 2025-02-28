<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'created_at', 'updated_at', 'price', 'stock_id'];

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

}
