<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id',
        'name',
        'amount',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    
}
