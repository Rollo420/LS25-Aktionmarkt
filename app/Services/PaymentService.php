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
}