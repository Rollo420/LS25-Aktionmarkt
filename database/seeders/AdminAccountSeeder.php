<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bank;

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
                'balance' => 10000.0,
            ])->credits()->create([
                'bank_id' => $woodly->bank->id,
                'name' => 'KFZ-Sparplan',
                'amount' => 1000.0,
                'interest_rate' => 5.0,
            ]);
            $woodly->roles()->attach(1); // Rolle 1 ist der Administrator

            $transactions = [
                [
                    'user_id' => $woodly->id,
                    'stock_id' => 2,
                    'status' => 'closed',
                    'type' => 'buy',
                    'quantity' => 15,
                ],
                [
                    'user_id' => $woodly->id,
                    'stock_id' => 4,
                    'status' => 'open',
                    'type' => 'sell',
                    'quantity' => 30,
                ],
            ];
            foreach ($transactions as $transaction) {
                $woodly->transactions()->create($transaction);
            }
        }

        if (!User::where('email', 'maro@bt21.com')->exists()) {
            $maro = User::factory()->create([
                'name' => 'TryS_Makaroni',
                'email' => 'maro@bt21.com',
                'password' => bcrypt('password'), // Passwort verschlüsseln
            ]);
            $maro->bank()->create([
                'iban' => Bank::generateIban(),
                'balance' => 10000.0,
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
                    'status' => 'open',
                    'type' => 'buy',
                    'quantity' => 10,
                ],
                [
                    'user_id' => $maro->id,
                    'stock_id' => 5,
                    'status' => 'closed',
                    'type' => 'sell',
                    'quantity' => 20,
                ],
            ];
            foreach ($transactions as $transaction) {
                $maro->transactions()->create($transaction);
            }
        }
    }
}
