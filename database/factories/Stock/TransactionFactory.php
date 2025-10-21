<?php

namespace Database\Factories\Stock;

use App\Models\ProductType;
use App\Models\GameTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Stock\Stock;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $stock_id = 1;

    public function definition(): array
    {
        $type = fake()->randomElement(['buy', 'sell', 'deposit', 'withdraw', 'transfer']);
        $stockId = ($type === 'buy' || $type === 'sell') ? Stock::inRandomOrder()->first()?->id ?? 1 : null;
        $priceAtBuy = 0; // Default auf 0 setzen, da Spalte nicht NULL erlaubt

        if ($type === 'buy') {
            // Aktueller Preis der Aktie zum Zeitpunkt der Transaktion ermitteln
            $stock = Stock::find($stockId);
            if ($stock) {
                $priceAtBuy = $stock->getCurrentPrice();
            } else {
                $priceAtBuy = fake()->randomFloat(2, 10, 500); // Fallback für zufällige Daten
            }
        }

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? 1,
            'game_time_id' => GameTime::inRandomOrder()->first()?->id ?? 1,
            'stock_id' => $stockId,
            // Status: boolean in DB. true = active/open/pending, false = closed/finished
            'status' => fake()->boolean(),
            'quantity' => fake()->numberBetween(1, 100),
            'type' => $type,
            'price_at_buy' => $priceAtBuy,
        ];
    }
}
