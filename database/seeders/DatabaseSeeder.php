<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//My imports Model
use App\Models\Account\account;
use App\Models\Account\password;
use App\Models\Stock\month;

//My imports Seeder
use Database\Seeders\Stock\monthSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $monthSeeder = new monthSeeder();

        //username::factory(5)->create();
        password::factory(5)->create();
        account::factory(5)->create();                
        $monthSeeder->CreateAllMonth();

        // User::factory(10)->create();
        //User::factory()->create([
        //    'name' => 'Test User',
        //    'email' => 'test@example.com',
        //]);
    }
}
