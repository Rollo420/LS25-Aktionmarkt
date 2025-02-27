<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class stock extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'price_id', 'product_types_id'];
}
