<?php

namespace App\Http\Controllers\adminsite;

use Illuminate\Http\Request;
use App\Models\FoodOrderDetails;
use App\Http\Controllers\Controller;

class FoodOrderDetailController extends Controller
{
    public function getAllFoodOrderDetails()
    {
        $data = FoodOrderDetails::with(['order', 'product'])->get();
        return $data;
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ]);
    }
}
