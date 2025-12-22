<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bank;
use App\Models\Stock\Price;
use App\Models\GameTime;
use App\Services\GameTimeService;
use App\Models\Dividend;

class FirstClearSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create initial GameTime at year 2000 to fix start date
        GameTime::create([
            'name' => '2000-01-01'
        ]);
    
        $this->call(ConfigSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(ProductTypeSeeder::class);
        
        $bt21Seeder = new \Database\Seeders\BT21StockSeeder();
        $bt21Stocks = $bt21Seeder->run();

        $config = \App\Models\Config::where('name', 'Default Config')->first();

        $bt21Stocks->map(function ($stock) use ($config) {
            $stock->configs()->attach($config->id);
            
            Price::factory()->create([
                'stock_id' => $stock->id,
            ]);

            Dividend::factory()->create([
                'stock_id' => $stock->id,
                'game_time_id' => GameTime::getCurrentGameTime()->id,
            ]);
        });




        $adminAcc = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@projekt.com',
            'password' => bcrypt('Admin'), // Passwort verschlüsseln
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
        $adminAcc->roles()->attach(id: 2); // Rolle 1 ist der Administrator
    }
}
