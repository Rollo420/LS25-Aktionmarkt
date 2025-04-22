<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product_type extends Model
{
    use HasFactory;
    /**
     * Beziehung: Ein Produkttyp hat viele Aktien.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stock()
    {
        return $this->hasMany(Stock::class);
    }
}
