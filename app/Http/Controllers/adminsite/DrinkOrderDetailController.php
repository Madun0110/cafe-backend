<?php

namespace App\Http\Controllers\adminsite;

use Illuminate\Http\Request;
use App\Models\DrinkOrderDetails;
use App\Http\Controllers\Controller;

class DrinkOrderDetailController extends Controller
{
    public function getAllDrinkOrderDetails()
    {
        $data = DrinkOrderDetails::with(['order', 'product'])->get();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ]);
    }
}
