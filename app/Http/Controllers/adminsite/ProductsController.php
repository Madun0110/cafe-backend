<?php

namespace App\Http\Controllers\adminsite;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public function getAllProducts()
    {
        $data = Products::all();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function singleProduct($id)
    {
        $data = Products::find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function createProduct(Request $request)
    {
        $validator = Validator::make([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'is_available' => $request->is_available,
            'category_id' => $request->category_id,
        ], [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'is_available' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'data' => $validator->errors()
            ], 422);
        }
        $data = Products::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $request->image,
            'category_id' => $request->category_id,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ], 201);
    }

    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'is_available' => $request->is_available,
            'category_id' => $request->category_id,
        ], [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'is_available' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'data' => $validator->errors()
            ], 422);
        }
        $data = Products::find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ], 404);
        }
        $data->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $request->image,
            'category_id' => $request->category_id,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function deleteProduct($id)
    {
        $data = Products::find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ], 404);
        }
        $data->delete();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => null
        ], 200);
    }
}
