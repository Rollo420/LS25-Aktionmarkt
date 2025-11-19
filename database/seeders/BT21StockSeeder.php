<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Collection;

class BT21StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): ?Collection // Der Typ wird auf Ramsey\Collection\Collection aufgelöst.
    {

        // Erstellung der Models und Speicherung in einer Variablen
        $stocks = Stock::factory()->createMany([
            [
                'name' => 'RJ Inc.',
                'firma' => 'BT21',
                'sektor' => 'Entertainment',
                'land' => 'Südkorea',
                'description' => 'RJ ist ein liebenswerter Alpaka-Charakter aus der BT21-Reihe, bekannt für seine flauschige Erscheinung und seinen freundlichen Charakter. Er trägt oft einen Schal und symbolisiert Wärme und Komfort.',
                'net_income' => 1200000.00,
                'dividend_frequency' => 2,
            ],
            [
                'name' => 'TATA Ltd.',
                'firma' => 'BT21',
                'sektor' => 'Entertainment',
                'land' => 'Südkorea',
                'description' => 'TATA ist ein einzigartiger Charakter mit einem herzförmigen Kopf und einer außerirdischen Herkunft. Er ist abenteuerlustig und neugierig, was ihn zu einem faszinierenden Mitglied der BT21-Familie macht.',
                'net_income' => 1500000.00,
                'dividend_frequency' => 2,
            ],
        ]);

        return $stocks; // Rückgabe der erstellten Collection
    }
}
