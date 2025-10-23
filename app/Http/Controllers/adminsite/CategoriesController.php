<?php

namespace App\Http\Controllers\adminsite;

use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function getAllCategories()
    {
        $data = Categories::all();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function singleCategory($id)
    {
        $data = Categories::find($id);
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

    public function createCategory(Request $request)
    {
        $validator = Validator::make([
            'name' => $request->name,
        ], [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'data' => $validator->errors()
            ], 422);
        }
        $data = Categories::create([
            'name' => $request->name
        ]);
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $validator = Validator::make([
            'name' => $request->name,
        ],[
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'data' => $validator->errors()
            ], 422);
        }
        $data = Categories::find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => null
            ], 404);
        }
        $data->update([
            'name' => $request->name
        ]);
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data
        ],200);
    }

    public function deleteCategory($id)
    {
        $data = Categories::find($id);
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
        ],200);
    }
}
