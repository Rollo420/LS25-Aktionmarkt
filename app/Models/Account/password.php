<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \App\Models\Account\account;

class password extends Model
{
    use HasFactory;
   
    protected $fillable = ['id', 'created_at', 'updated_at', 'hash'];

    public function accounts()
    {
        return $this->hasMany(account::class);
    }
}
