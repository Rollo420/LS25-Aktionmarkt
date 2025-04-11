<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;

//My imports Model Account
use App\Models\Account\Account;
use App\Models\Account\Password;

//My imports Model Stock
use App\Models\Stock\Price;
use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
use App\Models\Role;
use App\Models\Bank;
use App\Models\Credit;


//My Factory
use \Database\Factories\UserRoleFactory;

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
       
        
        Stock::factory(5)->create();
        Transaction::factory(5)->create();
        
<<<<<<< HEAD
  
        User::factory()->hasBank(1)->create([
=======
        $this->call(class: PriceSeeder::class);

        User::factory()->create([
>>>>>>> 02da501c24f2a750639cf4567fa03c44f5362008
            'name' => 'Woodly',
            'email' => 'woodly@gmail.com',
            'password' => bcrypt('password'), // Passwort verschlüsseln
        ]);
        User::factory(5)->hasBank(1)->create();

        $this->call(class: RoleSeeder::class);
        
        

        for ($i = 0; $i <= 4; $i++)
        {
            UserRoleFactory::new()->definition();
        }

        Credit::factory(5)->create();

        //Stock::factory(5)->create();
        //Transaction::factory(5)->create();
  
        //User::factory()->create([
        //    'name' => 'Woodly',
        //    'email' => 'woodly@gmail.com',
        //    'password' => bcrypt('password'), // Passwort verschlüsseln
        //]);
    }
}
