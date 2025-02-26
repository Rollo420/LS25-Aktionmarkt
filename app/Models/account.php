<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\username;

class account extends Model
{
    use HasFactory;
    protected $fillable = [ 'is_verified', 'password_id'];
    protected $hidden = ['username_id', 'id', 'mail_id',  ];

    //public function details()
    //{
    //    return $this->belongsTo(username::class, 'username_id'); 
    //}
   
}
