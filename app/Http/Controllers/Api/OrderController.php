<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Jobs\SendWarehouseEmailJob;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    use HelperTrait;


    public function store(OrderRequest $request)
    {
        $requested_products = collect($request->products);

        $products  = Product::with('ingredients')->whereIn('id', $requested_products->pluck('product_id'))->get();

        $data =  $this->prepareIngredients($requested_products, $products);

        DB::beginTransaction();
        try {
            $order = Order::create();
            $order_products = $this->mapOrderProducts($requested_products, $order->id);

            OrderProduct::insert($order_products);
            foreach ($data as $ingredient_id => $quantity) {
                $ingredient = Ingredient::whereId($ingredient_id)->first();
                if($ingredient->stock - $quantity < 0){

                    throw ValidationException::withMessages([
                        'error' => "$ingredient->name  is out of quantity"
                    ]);
                }
                $ingredient->stock -= $quantity;
                $ingredient->save();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(['message' => $th->getMessage()]);

            return response()->json([
                'message' => 'Something went wrong!'
            ], 500);
        }

        return response()->json([
            'message' => 'created successfully'
        ], 201);
    }

    function mapOrderProducts($products, $order_id){
        $order_products = $products->map(function ($product) use ($order_id) {
            $product['order_id'] = $order_id;
            return $product;
        })->toArray();
        return $order_products;
    }
    function prepareIngredients($requested_products, $products)
    {
        foreach ($requested_products as $requested_product) {
            $product_quantity = $requested_product['quantity'];
            $product = $products->firstWhere('id', $requested_product['product_id']);

            $product_ingredients = $product->ingredients;
            if ($product_ingredients) {
                foreach ($product_ingredients as $ingredient) {
                    $ingredient_id = $ingredient->id;

                    $product_ingredient_quantity = $this->convertQuantityUnit($ingredient->pivot->unit, $ingredient->unit, $ingredient->pivot->amount) * $product_quantity;

                    $data[$ingredient_id] = ($data[$ingredient_id] ?? 0) + $product_ingredient_quantity;
                }
            }
        }
        return $data;
    }
}
