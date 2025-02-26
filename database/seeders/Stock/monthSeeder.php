<?php

namespace Database\Seeders\Stock;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Stock\month;

class monthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //month::factory(count: 12)->create();

        $this->CreateAllMonth();

    }

    public function CreateAllMonth(): void
    {
        month::create(['name' => 'January']);
        month::create(['name' => 'February']);
        month::create(['name' => 'March']);
        month::create(['name' => 'April']); 
        month::create(['name' => 'May']);
        month::create(['name' => 'June']);
        month::create(['name' => 'July']);
        month::create(['name' => 'August']);
        month::create(['name' => 'September']);
        month::create(['name' => 'October']);
        month::create(['name' => 'November']);
        month::create(['name' => 'December']);
    }
}
