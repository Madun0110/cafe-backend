<?php

namespace App\Http\Controllers\adminsite;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
<<<<<<< HEAD
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;



#[OA\Tag(
    name: "Admin Products",
    description: "Manajemen produk untuk admin (CRUD produk beserta upload gambar)"
)]
class ProductsController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "Ambil semua produk",
        description: "Mengambil seluruh data produk beserta relasi kategori",
        tags: ["Admin Products"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil mengambil daftar produk",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "success"),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object")),
                    ]
                )
            )
        ]
    )]
    public function getAllProducts()
    {
        $data = Products::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => $data,
        ]);
    }

    #[OA\Get(
        path: "/api/products/{id}",
        summary: "Ambil detail produk",
        description: "Mengambil satu produk berdasarkan ID beserta kategorinya",
        tags: ["Admin Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID produk"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Produk ditemukan"),
            new OA\Response(response: 404, description: "Produk tidak ditemukan")
        ]
    )]
    public function singleProduct($id)
    {
        $data = Products::with('category')->find($id);

        if (!$data) {
            return response()->json([
                'status'  => false,
                'message' => 'Data not found',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => $data,
        ]);
    }

    #[OA\Post(
        path: "/api/products",
        summary: "Tambah produk baru",
        description: "Membuat produk baru dengan upload gambar (multipart/form-data)",
        tags: ["Admin Products"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["name", "description", "price", "is_available", "category_id"],
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "Cappuccino"),
                        new OA\Property(property: "description", type: "string", example: "Kopi susu panas"),
                        new OA\Property(property: "price", type: "number", format: "float", example: 25000),
                        new OA\Property(property: "is_available", type: "boolean", example: true),
                        new OA\Property(property: "category_id", type: "integer", example: 1),
                        new OA\Property(
                            property: "image",
                            type: "string",
                            format: "binary",
                            nullable: true,
                            description: "File gambar produk"
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Produk berhasil dibuat"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function createProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'price'        => 'required|numeric',
            'is_available' => 'required|boolean',
            'category_id'  => 'required|exists:categories,id',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'validation error',
                'data'    => $validator->errors(),
            ], 422);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $data = Products::create([
            'name'         => $request->name,
            'description'  => $request->description,
            'price'        => $request->price,
            'image'        => $imagePath,
            'category_id'  => $request->category_id,
            'is_available' => $request->is_available,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => $data,
        ], 201);
    }

    #[OA\Put(
        path: "/api/products/{id}",
        summary: "Update produk",
        description: "Memperbarui data produk dan dapat mengganti gambar",
        tags: ["Admin Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["name", "description", "price", "is_available", "category_id"],
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "Latte"),
                        new OA\Property(property: "description", type: "string", example: "Kopi susu creamy"),
                        new OA\Property(property: "price", type: "number", format: "float", example: 22000),
                        new OA\Property(property: "is_available", type: "boolean", example: true),
                        new OA\Property(property: "category_id", type: "integer", example: 1),
                        new OA\Property(
                            property: "image",
                            type: "string",
                            format: "binary",
                            nullable: true,
                            description: "Upload gambar baru (opsional)"
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Produk berhasil diupdate"),
            new OA\Response(response: 404, description: "Produk tidak ditemukan"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'price'        => 'required|numeric',
            'is_available' => 'required|boolean',
            'category_id'  => 'required|exists:categories,id',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'validation error',
                'data'    => $validator->errors(),
            ], 422);
        }

        $data = Products::find($id);

        if (!$data) {
            return response()->json([
                'status'  => false,
                'message' => 'Data not found',
                'data'    => null,
            ], 404);
        }

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('products', 'public');

            if (!empty($data->image) && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }

            $data->image = $newImagePath;
        }

        $data->name         = $request->name;
        $data->description  = $request->description;
        $data->price        = $request->price;
        $data->category_id  = $request->category_id;
        $data->is_available = $request->is_available;
        $data->save();

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => $data,
        ], 200);
    }

    #[OA\Delete(
        path: "/api/products/{id}",
        summary: "Hapus produk",
        description: "Menghapus produk beserta gambar yang tersimpan",
        tags: ["Admin Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Produk berhasil dihapus"),
            new OA\Response(response: 404, description: "Produk tidak ditemukan")
        ]
    )]
    public function deleteProduct($id)
    {
        $data = Products::find($id);

        if (!$data) {
            return response()->json([
                'status'  => false,
                'message' => 'Data not found',
                'data'    => null,
            ], 404);
        }

        if (!empty($data->image) && Storage::disk('public')->exists($data->image)) {
            Storage::disk('public')->delete($data->image);
        }

        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => null,
=======

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
            'is_available' => $request->is_available,
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
            'is_available' => $request->is_available,
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
>>>>>>> 4cc37ca3044044fe7495c893dd27c9b0dc94a62d
        ], 200);
    }
}
