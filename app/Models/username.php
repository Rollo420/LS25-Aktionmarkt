<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\account;
class username extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
    ];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    //public function accounts()
    //{
    //    return $this->hasMany(account::class);
    //}
    
}
