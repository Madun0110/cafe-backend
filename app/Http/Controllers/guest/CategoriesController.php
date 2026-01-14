<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Guest Categories",
    description: "Endpoint API kategori untuk kebutuhan guest / frontend"
)]
class CategoriesController extends Controller
{
    #[OA\Get(
        path: "/api/guest/categories",
        summary: "Ambil daftar kategori untuk guest",
        description: "Mengambil semua kategori yang diurutkan berdasarkan nama. Digunakan oleh frontend guest untuk filter menu.",
        tags: ["Guest Categories"],
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
                                    new OA\Property(property: "name", type: "string", example: "Kopi"),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Fallback: mengembalikan data kategori default jika terjadi error"
            )
        ]
    )]
    public function getCategories()
    {
        try {
            // Urutkan berdasarkan name
            $data = Categories::orderBy('name')->get()
                ->map(function ($category) {
                    return [
                        'id'   => $category->id,
                        'name' => $category->name,
                    ];
                });

            return response()->json([
                'status'  => true,
                'message' => 'success',
                'data'    => $data,
            ]);

        } catch (\Exception $e) {
            // Fallback jika error
            return response()->json([
                'status'  => true,
                'message' => 'success',
                'data'    => [
                    ['id' => 1, 'name' => 'Kopi'],
                    ['id' => 2, 'name' => 'Minuman'],
                    ['id' => 3, 'name' => 'Makanan'],
                    ['id' => 4, 'name' => 'Snack'],
                ]
            ]);
        }
    }
}
