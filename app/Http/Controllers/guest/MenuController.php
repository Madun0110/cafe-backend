<?php

namespace App\Http\Controllers\guest;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function getProducts(Request $request)
    {
        $products = Products::where('is_available', true);
        if ($request->filled('category_id')) {
            $products->where('category_id', $request->category_id);
        }
        $products = $products->get();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $products
        ], 200);
    }
}
