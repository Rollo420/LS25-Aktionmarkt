<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Account\Password;
use App\Models\Stock\Transaction;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [ 'is_verified', 'password_id'];
    protected $hidden = ['id', 'mail_id' ];

    public function password()
    {
        return $this->belongsTo(Password::class, 'password_id'); 
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
   
}
