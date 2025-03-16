<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'created_at', 'updated_at', 'price', 'stock_id', 'month', 'year', 'name'];

    //protected static function boot()
    //{
    //    parent::boot();
//
    //    static::creating(function ($model) {
    //        $maxYear = static::max('year');
    //        $model->year = $maxYear ? $maxYear + 1 : 1;
    //    });
    //}

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

}
