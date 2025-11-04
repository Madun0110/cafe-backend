<?php

namespace App\Http\Controllers\adminsite;

use Illuminate\Http\Request;
use App\Models\FoodOrderDetails;
use App\Http\Controllers\Controller;
use App\Models\Orders;

class FoodOrderDetailController extends Controller
{
    public function getAllFoodOrderDetails()
    {
        $data = Orders::with(['food'])->get();
        // return $data;
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ]);
    }
}
