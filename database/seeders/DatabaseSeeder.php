<?php

namespace Database\Seeders;

use App\Models\Dividend;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;

//My imports Model Stock
use App\Models\Stock\Price;
use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
use App\Models\Role;
use App\Models\Bank;
use App\Models\Credit;
use App\Models\Month;
use App\Models\GameTime;
use App\Models\ProductType;


//My Factory
use \Database\Factories\UserRoleFactory;
use \Database\Seeders\AdminAccountSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GameTimeSeeder::class,
            ProductType::class,
            StockSeeder::class,
            PriceSeeder::class,
        ]);

        User::factory(5)->hasBank()->create();
        Transaction::factory(5)->create();

        $this->call(class: RoleSeeder::class);
        

        for ($i = 0; $i <= 4; $i++)
        {
            UserRoleFactory::new()->definition();
        }

        // Erstelle für jede existierende Bank einen Credit
        $banks = Bank::all();
        foreach ($banks as $bank) {
            Credit::factory()->create(['bank_id' => $bank->id]);
        }

        $this->call(class: AdminAccountSeeder::class);
        
        //Stock::factory(5)->create();
        //Transaction::factory(5)->create();
  
        //User::factory()->create([
        //    'name' => 'Woodly',
        //    'email' => 'woodly@gmail.com',
        //    'password' => bcrypt('password'), // Passwort verschlüsseln
        //]);
    }
}
