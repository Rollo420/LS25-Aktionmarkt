<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Farm;
use App\Models\User;

class FarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $farms = Farm::factory()->count(10)->create();
       $user = User::findorFail(6);

       $user->farms()->create([
        'name' => 'Test Farm',
       ]);

    }
}
