<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bank;

class FirstClearSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $this->call(RoleSeeder::class);
        $this->call(ProductTypeSeeder::class);
        $this->call(StockSeeder::class);
        
        $adminAcc = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@projekt.com',
            'password' => bcrypt('Admina'), // Passwort verschlüsseln
        ]);
        $adminAcc->bank()->create([
            'iban' => Bank::generateIban(),
            'balance' => 1000000.0, // 1 Million Euro für Admin
        ])->credits()->create([
            'bank_id' => $adminAcc->bank->id,
            'name' => 'KFZ-Sparplan',
            'amount' => 1000.0,
            'interest_rate' => 5.0,
        ]);
        $adminAcc->roles()->attach(id: 1); // Rolle 1 ist der Administrator
    }
}
