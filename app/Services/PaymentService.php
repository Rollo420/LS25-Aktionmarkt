<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Bank;

class PaymentService
{
    public static function checkUserBalance($toBalance)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $user->bank->refresh();

        if ($user->bank->balance < $toBalance) {
            throw new \Exception('Nicht genug Guthaben für die Auszahlung!');
        }

        return true;
    }

    /**
     * Provision berechnen (statische Fallback-Werte, keine Config-Service-Abhängigkeit)
     */
    public static function calculateFees($amount, $transactionType = 'buy')
    {
        // Direkte Fallback-Werte verwenden, ConfigService wurde entfernt per Nutzerwunsch
        $buyFee = 0.01;  // 1%
        $sellFee = 0.015; // 1.5%

        $feeRate = $transactionType === 'buy' ? $buyFee : $sellFee;
        return $amount * $feeRate;
    }

    /**
     * Gesamtzahlung mit Gebühren berechnen
     */
    public static function calculateTotalWithFees($baseAmount, $transactionType = 'buy')
    {
        $fees = self::calculateFees($baseAmount, $transactionType);
        return $baseAmount + $fees;
    }
}