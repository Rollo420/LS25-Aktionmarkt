<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = collect([
            // Feldfrüchte
            'Weizen',
            'Gerste',
            'Raps',
            'Mais',
            'Hafer',
            'Kartoffeln',
            'Zuckerrohr',
            'Zuckerrüben',
            'Sojabohnen',
            'Hanf',
            'Kleesaat',
            'Grassilage',

            // Tierprodukte
            'Milch',
            'Eier',
            'Wolle',
            'Fleisch',

            // Forstwirtschaft
            'Holz',
            'Stroh',
            'Heu',

            // Verarbeitete Produkte / Industrie
            'Mehl',
            'Futtermittel',
            'Pellets',
            'Bioenergie',
            'Biodiesel',
        ]);

        foreach($products as $product)
        {
            ProductType::firstOrCreate([
                'name' => $product,
            ]);
        }

    }
}
