<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bank;
use App\Models\GameTime;

class AdminAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Erstelle einen Administrator-Account
        //Jan Account
        if (!User::where('email', 'woodly@gmail.com')->exists()) {
            $woodly = User::factory()->create([
                'name' => 'woodly',
                'email' => 'woodly@gmail.com',
                'password' => bcrypt('password'), // Passwort verschlüsseln
            ]);
            $woodly->bank()->create([
                'iban' => Bank::generateIban(),
                'balance' => 1000000.0, // 1 Million Euro für Admin
            ])->credits()->create([
                'bank_id' => $woodly->bank->id,
                'name' => 'KFZ-Sparplan',
                'amount' => 1000.0,
                'interest_rate' => 5.0,
            ]);
            $woodly->roles()->attach(id: 1); // Rolle 1 ist der Administrator

            $transactions = [
                [
                    'user_id' => $woodly->id,
                    'stock_id' => 2,
                    'status' => true, // closed -> true (sofort ausgeführt)
                    'type' => 'buy',
                    'quantity' => 15,
                    'game_time_id' => (new GameTime()->getCurrentGameTime())->id,
                    'price_at_buy' => \App\Models\Stock\Stock::find(2)?->getCurrentPrice() ?? 37.54, // Aktueller Preis der Aktie
                ],
                [
                    'user_id' => $woodly->id,
                    'stock_id' => 4,
                    'status' => true, // closed -> true (sofort ausgeführt)
                    'type' => 'sell',
                    'quantity' => 30,
                    'game_time_id' => (new GameTime()->getCurrentGameTime())->id,
                    'price_at_buy' => null, // Sell-Transaktionen brauchen keinen price_at_buy
                ],
            ];
            foreach ($transactions as $transaction) {
                $woodly->transactions()->create($transaction);
            }
        }

        if (!User::where('email', 'maro@bt21.com')->exists()) {
            $maro = User::factory()->create([
                'name' => 'TryS_Makaroni',
                'email' => 'bt21@trys.net',
                'password' => bcrypt('password'), // Passwort verschlüsseln
            ]);
            $maro->bank()->create([
                'iban' => Bank::generateIban(),
                'balance' => 1000000.0, // 1 Million Euro für Admin
            ])->credits()->create([
                'bank_id' => $maro->bank->id,
                'name' => 'Bausparvertrag',
                'amount' => 1000.0,
                'interest_rate' => 5.0,
            ]);
            $maro->roles()->attach(1); // Rolle 1 ist der Administrator

            $transactions = [
                [
                    'user_id' => $maro->id,
                    'stock_id' => 1,
                    'status' => true, // closed -> true (sofort ausgeführt)
                    'type' => 'buy',
                        'quantity' => 10,
                        'game_time_id' => (new \App\Services\GameTimeService())->getOrCreate(\Carbon\Carbon::create((int)date('Y'), (int)date('m'), 1))->id,
                        'price_at_buy' => \App\Models\Stock\Stock::find(1)?->getCurrentPrice() ?? 25.00, // Aktueller Preis der Aktie
                ],
                [
                    'user_id' => $maro->id,
                    'stock_id' => 5,
                    'status' => true, // closed -> true (sofort ausgeführt)
                    'type' => 'sell',
                        'quantity' => 20,
                        'game_time_id' => (new \App\Services\GameTimeService())->getOrCreate(\Carbon\Carbon::create((int)date('Y'), (int)date('m'), 1))->id,
                        'price_at_buy' => null, // Sell-Transaktionen brauchen keinen price_at_buy
                ],
            ];
            foreach ($transactions as $transaction) {
                $maro->transactions()->create($transaction);
            }
        }
    }
}
