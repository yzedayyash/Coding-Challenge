<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = Product::create([
            'name' => 'product1',
            'price' => 5,
        ]);

        $ingredient = Ingredient::insert([[
            'name' => 'beef',
            'stock' => 20,
            'unit' => 'kg',
            'alert_on' => 10
        ],
        [
            'name' => 'cheese',
            'stock' => 5,
            'unit' => 'kg',
            'alert_on' => 2.5
        ],
        [
            'name' => 'onion',
            'stock' => 1,
            'unit' => 'kg',
            'alert_on' => 0.5
        ],
    ] );
    $product->ingredients()->attach(1, ['amount' => 150]);
    $product->ingredients()->attach(2, ['amount' => 30]);
    $product->ingredients()->attach(3, ['amount' => 20]);

    }


}
