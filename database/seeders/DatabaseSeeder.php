<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//My imports Model Account
use App\Models\Account\Account;
use App\Models\Account\Password;

//My imports Model Stock
use App\Models\Stock\Price;
use App\Models\Stock\Product_type;
use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Account
        Password::factory(5)->create();
        Account::factory(5)->create();  

        //Stock
        Price::factory(5)->create();
        Product_type::factory(5)->create();
        Stock::factory(5)->create();
        Transaction::factory(5)->create();
  
        

        //User::factory()->create([
        //    'name' => 'Woodly',
        //    'email' => 'woodly@gmail.com',
        //    'password' => bcrypt('password'), // Passwort verschlüsseln
        //]);

        
    }
}
