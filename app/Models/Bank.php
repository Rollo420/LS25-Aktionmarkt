<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Models\User;
use App\Models\Credit;
use \Illuminate\Database\Eloquent\Factories\HasFactory;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        
        'balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }
}
