<?php

namespace App\Http\Controllers\adminsite;

use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Admin Categories",
    description: "Endpoint CRUD kategori untuk admin site"
)]
class CategoriesController extends Controller
{
    #[OA\Get(
        path: "/api/categories",
        summary: "Ambil semua kategori",
        description: "Mengambil seluruh data kategori yang diurutkan berdasarkan nama",
        tags: ["Admin Categories"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil mengambil data kategori",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "success"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Minuman"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 500, description: "Gagal memuat kategori")
        ]
    )]
    public function getAllCategories()
    {
        try {
            $data = Categories::orderBy('name')->get([
                'id',
                'name',
                'created_at',
                'updated_at',
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'success',
                'data'    => $data,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to load categories',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Get(
        path: "/api/categories/{id}",
        summary: "Ambil detail kategori",
        description: "Mengambil satu kategori berdasarkan ID",
        tags: ["Admin Categories"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID kategori"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Kategori ditemukan",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Minuman"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Kategori tidak ditemukan")
        ]
    )]
    public function singleCategory($id)
    {
        $category = Categories::find($id, [
            'id',
            'name',
            'created_at',
            'updated_at',
        ]);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'success',
            'data'    => $category,
        ], 200);
    }

    #[OA\Post(
        path: "/api/categories",
        summary: "Tambah kategori baru",
        description: "Membuat kategori baru",
        tags: ["Admin Categories"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Snack")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Kategori berhasil dibuat",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Category created"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 5),
                                new OA\Property(property: "name", type: "string", example: "Snack"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'validation error',
                'data'    => $validator->errors(),
            ], 422);
        }

        $category = Categories::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Category created',
            'data'    => $category,
        ], 201);
    }

    #[OA\Put(
        path: "/api/categories/{id}",
        summary: "Update kategori",
        description: "Memperbarui data kategori berdasarkan ID",
        tags: ["Admin Categories"],
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
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Minuman Panas")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Kategori berhasil diupdate"),
            new OA\Response(response: 404, description: "Kategori tidak ditemukan"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function updateCategory(Request $request, $id)
    {
        $category = Categories::find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
                'data'    => null,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'validation error',
                'data'    => $validator->errors(),
            ], 422);
        }

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Category updated',
            'data'    => $category,
        ], 200);
    }

    #[OA\Delete(
        path: "/api/categories/{id}",
        summary: "Hapus kategori",
        description: "Menghapus kategori berdasarkan ID",
        tags: ["Admin Categories"],
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
            new OA\Response(response: 200, description: "Kategori berhasil dihapus"),
            new OA\Response(response: 404, description: "Kategori tidak ditemukan")
        ]
    )]
    public function deleteCategory($id)
    {
        $category = Categories::find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
                'data'    => null,
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Category deleted',
            'data'    => null,
        ], 200);
    }
}
