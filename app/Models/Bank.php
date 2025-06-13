<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\User;
use App\Models\Credit;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'iban',
        'balance',
    ];

    protected static function booted()
    {
        static::creating(function ($bank) {
            // Generiere eine IBAN, falls keine angegeben wurde
            if (empty($bank->iban)) {
                $bank->iban = self::generateIban();
                $bank->balance = 0.0; // Setze den Anfangsbestand auf 0
            }
        });
}  
    

    public static function generateIban(): string
    {
        $countryCode = 'DE';
        $bankCode = '12345678'; // Beispiel-Bankleitzahl
        $accountNumber = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT); // Zufällige Kontonummer
        $checkDigits = '00'; // Platzhalter für Prüfziffern

        // Kombiniere die Teile zur IBAN
        $iban = $countryCode . $checkDigits . $bankCode . $accountNumber;

        // Berechne die Prüfziffern
        $numericIban = str_replace(
            range('A', 'Z'),
            range(10, 35),
            substr($iban, 4) . substr($iban, 0, 4)
        );
        $checkDigits = 98 - bcmod($numericIban, 97);

        // Setze die berechneten Prüfziffern ein
        return $countryCode . str_pad($checkDigits, 2, '0', STR_PAD_LEFT) . $bankCode . $accountNumber;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }
}
