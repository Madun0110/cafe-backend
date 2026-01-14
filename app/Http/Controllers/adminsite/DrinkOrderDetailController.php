<?php

namespace App\Http\Controllers\adminsite;

use App\Models\Orders;
use Illuminate\Http\Request;
use App\Models\DrinkOrderDetails;
use App\Http\Controllers\Controller;

class DrinkOrderDetailController extends Controller
{
    public function getAllDrinkOrderDetails()
    {
        $data = Orders::with(['drink.product'])->get();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ]);
    }
}
