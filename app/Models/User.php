<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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

    /*protected static function booted()
    {
        static::created(function ($user) {
            // Automatisch einen Bankeintrag erstellen
            Bank::create([
                'user_id' => $user->id,
                'iban' => Bank::generateIban(), // Verwende die neue IBAN-Generierung
                'balance' => 4.0, // Standardwert für den Kontostand
            ]);
        });
    }*/
}
