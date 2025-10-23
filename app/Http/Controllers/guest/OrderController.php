<?php

namespace App\Http\Controllers\guest;

use App\Models\Orders;
use App\Models\Products;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\FoodOrderDetails;
use App\Models\DrinkOrderDetails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function orderProducts(Request $request){
        $validator = Validator::make([
            'table' => $request->table,
            'products' => $request->products,
            'total' => $request->total,
            'payment_method' => $request->payment_method
        ],[
            'table' => 'required',
            'products' => 'required',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer',
            'products.*.price' => 'required|numeric',
            'products.*.total' => 'required|numeric',
            'total' => 'required',
            'payment_method' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'data' => $validator->errors()
            ], 422);
        }

        $order = Orders::create([
            'table' => $request->table,
            'total' => $request->total
        ]);

        foreach ($request->products as $product) {
            $find = Products::find($product['product_id']);
            if($find->category_id == 1){
                FoodOrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total' => $product['total']
                ]);
            }else if($find->category_id == 2){
                DrinkOrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total' => $product['total']
                ]);
            }
        }

        $transactions = Transactions::create([
            'order_id' => $order->id,
            'payment_method' => $request->payment_method,
            'total' => $request->total
        ]);

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $transactions
        ],200);
    }
}
