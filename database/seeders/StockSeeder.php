<?php

namespace Database\Seeders;

use App\Services\GameTimeService;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Dividend;
use App\Models\Stock\Stock;
use App\Models\Stock\Price;
use App\Models\GameTime;
use App\Models\Config;

use App\Http\Controllers\TimeController;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Collection\Collection;

class StockSeeder extends Seeder
{
    public function run(): \Illuminate\Database\Eloquent\Collection
    {
        // Call BT21StockSeeder to get BT21 stocks
        $bt21Seeder = new BT21StockSeeder();
        $bt21Stocks = $bt21Seeder->run();

        // Create other stocks
        $otherStocks = Stock::factory()->createMany([
            [
                'name' => 'Apple Inc.',
                'firma' => 'Apple',
                'sektor' => 'Technology',
                'land' => 'USA',
                'description' => 'Apple Inc. ist ein multinationaler Technologiekonzern, der sich auf die Entwicklung und den Vertrieb von Elektronik, Software und Online-Diensten spezialisiert hat.',
                'net_income' => 100000000.00,
                'dividend_frequency' => 4,
            ],
            [
                'name' => 'Microsoft Corporation',
                'firma' => 'Microsoft',
                'sektor' => 'Technology',
                'land' => 'USA',
                'description' => 'Microsoft Corporation ist ein multinationaler Technologiekonzern, der Software, Elektronik und persönliche Computer entwickelt und vertreibt.',
                'net_income' => 80000000.00,
                'dividend_frequency' => 4,
            ],
            [
                'name' => 'Tesla Inc.',
                'firma' => 'Tesla',
                'sektor' => 'Automotive',
                'land' => 'USA',
                'description' => 'Tesla Inc. ist ein amerikanisches Unternehmen, das sich auf die Produktion von Elektrofahrzeugen, Solarpanele und Energiespeichersysteme spezialisiert hat.',
                'net_income' => 5000000.00,
                'dividend_frequency' => 2,
            ],
            [
                'name' => 'Amazon.com Inc.',
                'firma' => 'Amazon',
                'sektor' => 'E-commerce',
                'land' => 'USA',
                'description' => 'Amazon.com Inc. ist ein multinationaler E-Commerce- und Cloud-Computing-Konzern mit Sitz in Seattle, Washington.',
                'net_income' => 30000000.00,
                'dividend_frequency' => 4,
            ],
            [
                'name' => 'Alphabet Inc.',
                'firma' => 'Google',
                'sektor' => 'Technology',
                'land' => 'USA',
                'description' => 'Alphabet Inc. ist ein multinationaler Technologiekonzern, der Google und andere Unternehmen besitzt und sich auf Internetdienste, Software und Technologie spezialisiert hat.',
                'net_income' => 60000000.00,
                'dividend_frequency' => 4,
            ],
            [
                'name' => 'Meta Platforms Inc.',
                'firma' => 'Meta',
                'sektor' => 'Social Media',
                'land' => 'USA',
                'description' => 'Meta Platforms Inc. ist ein multinationaler Technologiekonzern, der sich auf soziale Medien, virtuelle Realität und Metaverse-Technologien spezialisiert hat.',
                'net_income' => 40000000.00,
                'dividend_frequency' => 4,
            ],
            [
                'name' => 'NVIDIA Corporation',
                'firma' => 'NVIDIA',
                'sektor' => 'Technology',
                'land' => 'USA',
                'description' => 'NVIDIA Corporation ist ein multinationaler Technologiekonzern, der sich auf die Entwicklung von Grafikprozessoren und KI-Technologien spezialisiert hat.',
                'net_income' => 20000000.00,
                'dividend_frequency' => 4,
            ],
            [
                'name' => 'Samsung Electronics Co., Ltd.',
                'firma' => 'Samsung',
                'sektor' => 'Technology',
                'land' => 'Südkorea',
                'description' => 'Samsung Electronics Co., Ltd. ist ein südkoreanischer Elektronikkonzern, der sich auf die Produktion von Elektronik, Halbleitern und Telekommunikationsgeräten spezialisiert hat.',
                'net_income' => 15000000.00,
                'dividend_frequency' => 4,
            ],
            [
                'name' => 'Toyota Motor Corporation',
                'firma' => 'Toyota',
                'sektor' => 'Automotive',
                'land' => 'Japan',
                'description' => 'Toyota Motor Corporation ist ein japanischer Automobilhersteller, der sich auf die Produktion von Fahrzeugen, Motoren und Zubehör spezialisiert hat.',
                'net_income' => 25000000.00,
                'dividend_frequency' => 2,
            ],
            [
                'name' => 'Sony Group Corporation',
                'firma' => 'Sony',
                'sektor' => 'Entertainment',
                'land' => 'Japan',
                'description' => 'Sony Group Corporation ist ein multinationaler Konzern, der sich auf die Produktion von Elektronik, Unterhaltung und Finanzdienstleistungen spezialisiert hat.',
                'net_income' => 10000000.00,
                'dividend_frequency' => 4,
            ],
        ]);

        // Combine the collections
        $allStocks = $bt21Stocks->merge($otherStocks);

        // Now, proceed with the rest of the logic for prices and dividends
        $faker = Faker::create();
        $gt = new GameTime();
        $gtService = new GameTimeService();
        $timeController = new TimeController();
        $config = Config::factory()->create(['name' => 'Default Config', 'description' => 'Dieses ist die standart Einstellungen'])->get()->first();

        // Erstelle 132 GameTimes von 2000-01-01 bis 2010-12-01
        $currentDate = Carbon::parse('2000-01-01');
        $gameTimes = [];
        for ($i = 0; $i < 132; $i++) {
            $gameTimes[] = $gtService->getOrCreate($currentDate);
            $currentDate = $currentDate->addMonth();
        }

        foreach ($bt21Stocks as $stock) {
            $lastPrice = 100.0; // Initialer Preis

            foreach ($gameTimes as $gt) {
                $monthIndex = (int) date('m', strtotime($gt->name)) - 1; // 0-based
                $newPrice = $timeController->generatePrice($lastPrice, $monthIndex);

                Price::create([
                    'stock_id' => $stock->id,
                    'game_time_id' => $gt->id,
                    'name' => $newPrice,
                ]);

                $lastPrice = $newPrice;

                // Dividende für den ersten GameTime erstellen
                Dividend::factory()->create([
                    'stock_id' => $stock->id,
                    'game_time_id' => $gt->id,
                    'amount_per_share' => $faker->randomFloat(2, 0.1, 5.0),
                ]);
            }

            $stock->configs()->attach($config->id);
        }

        return $allStocks;
    }
}
