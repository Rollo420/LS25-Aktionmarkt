<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

//My Models
use App\Models\Stock\Transaction;
use App\Models\Bank; // Importiere das Bank-Modell

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    public function isAdministrator()
    {
        return $this->roles()->where('name', 'admin')->exists();
    }

    public function bank(){
        return $this->hasOne(Bank::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getBankAccountBalance(): float
    {
        $bank = $this->bank;
        return $bank ? $bank->balance : 0.0;
    }

    public function addBankAccountBalance(float $amount): void
    {
        $bank = $this->bank;
        if ($bank) {
            $bank->balance += $amount;
            $bank->save();
        }
    }

    public function getBankAccountBalanceAttribute(): float
    {
        return $this->getBankAccountBalance();
    }

    public function getStockQuantity()
    {
        $this->transactions();
    }
}
