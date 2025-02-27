<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//My imports Model Account
use App\Models\Account\account;
use App\Models\Account\password;

//My imports Model Stock
use App\Models\Stock\price;
use App\Models\Stock\product_type;
use App\Models\Stock\stock;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        password::create([
            'id' => 111,
            'hash' => '1234567890',
        ]);

        account::create([
            'username' => 'test',
            'mail' => 'peteroderso@exampel.com',
            'is_verified' => 1,
            'password_id' => 111,
        ]);

        price::factory(5)->create();
        product_type::factory(5)->create();
        stock::factory(5)->create();

        //password::factory(5)->create();
        //account::factory(5)->create();    


        // User::factory(10)->create();
        //User::factory()->create([
        //    'name' => 'Test User',
        //    'email' => 'test@example.com',
        //]);
    }
}
