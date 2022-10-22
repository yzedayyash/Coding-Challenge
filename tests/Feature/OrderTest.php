<?php

namespace Tests\Feature;

use App\Jobs\SendWarehouseEmailJob;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseTransactions;


    public function test_create_order_without_sending_email()
    {

        $product_id =   $this->createProductsNotToAlert();

        $request_data = ['product_id' => $product_id, 'quantity' => rand(1, 5)];
        $data = [
            'products' => $request_data
        ];
        Queue::fake();
        $response = $this
            ->postJson('/api/order', [
                'products' => $data
            ]);

        $response->assertCreated();

        Queue::assertNotPushed(SendWarehouseEmailJob::class);
    }


    public function test_create_order_with_sending_email()
    {

        $product_id = $this->createProductsToAlert();

        $request_data = ['product_id' =>  $product_id, 'quantity' => 1];
        $data = [
            'products' => $request_data
        ];
        Queue::fake();

        $response = $this
            ->postJson('/api/order', [
                'products' => $data
            ]);
        $response->assertCreated();
        Queue::assertPushed(SendWarehouseEmailJob::class);
    }

    function createProductsNotToAlert()
    {
        $product = Product::create([
            'name' => 'product1',
            'price' => 5,
        ]);

        $first_ing = Ingredient::create(
            [
                'name' => 'beef',
                'stock' => 20,
                'unit' => 'kg',
                'alert_on' => 10
            ]
        );

        $second_ing = Ingredient::create([
            'name' => 'cheese',
            'stock' => 5,
            'unit' => 'kg',
            'alert_on' => 2.5
        ]);
        $third_ing = Ingredient::create([

            'name' => 'onion',
            'stock' => 1,
            'unit' => 'kg',
            'alert_on' => 0.5

        ]);
        $product->ingredients()->attach($first_ing->id, ['amount' => 150, 'unit' => 'g']);
        $product->ingredients()->attach($second_ing->id, ['amount' => 30, 'unit' => 'g']);
        $product->ingredients()->attach($third_ing->id, ['amount' => 20, 'unit' => 'g']);
        return $product->id;
    }


    function createProductsToAlert()
    {
        $product = Product::create([
            'name' => 'product1',
            'price' => 5,
        ]);



        $first_ing = Ingredient::create(
            [
                'name' => 'beef',
                'stock' => 20,
                'unit' => 'kg',
                'alert_on' => 10
            ]
        );

        $second_ing = Ingredient::create([
            'name' => 'cheese',
            'stock' => 5,
            'unit' => 'kg',
            'alert_on' => 2.5
        ]);
        $third_ing = Ingredient::create([

            'name' => 'onion',
            'stock' => 1,
            'unit' => 'kg',
            'alert_on' => 0.5

        ]);
        $product->ingredients()->attach($first_ing, ['amount' => 10, 'unit' => 'kg']);
        $product->ingredients()->attach($second_ing, ['amount' => 3, 'unit' => 'kg']);
        $product->ingredients()->attach($third_ing, ['amount' => 20, 'unit' => 'g']);
        return $product->id;
    }
}
