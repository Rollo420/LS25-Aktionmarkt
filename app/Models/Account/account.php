<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Account\password;

class account extends Model
{
    use HasFactory;
    protected $fillable = [ 'is_verified', 'password_id'];
    protected $hidden = ['id', 'mail_id' ];

    public function GetPassword()
    {
        return $this->belongsTo(password::class, 'password_id'); 
    }
   
}
